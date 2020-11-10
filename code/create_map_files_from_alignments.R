#!/usr/bin/env Rscript
# create table of aliases
# run from DB directory
library(tidyverse)
library(here)

# READ IN MAPPING FILE

blat_cols <- c(
    "matches", # Number of matching bases that aren't repeats.
    "misMatches", # Number of bases that don't match.
    "repMatches", # Number of matching bases that are part of repeats.
    "nCount", # Number of 'N' bases.
    "qNumInsert", # Number of inserts in query.
    "qBaseInsert", # Number of bases inserted into query.
    "tNumInsert", # Number of inserts in target.
    "tBaseInsert", # Number of bases inserted into target.
    "strand", # defined as + (forward) or, # (reverse) for query strand. In mouse, a second '+' or '-' indecates genomic strand.
    "qName", # Query sequence name.
    "qSize", # Query sequence size.
    "qStart", # Alignment start position in query.
    "qEnd", # Alignment end position in query.
    "tName", # Target sequence name.
    "tSize", # Target sequence size.
    "tStart", # Alignment start position in target.
    "tEnd", # Alignment end position in target.
    "blockCount", # Number of blocks in the alignment.
    "blockSizes", # Comma-separated list of sizes of each block.
    "qStarts", # Comma-separated list of start position of each block in query.
    "tStarts" # Comma-separated list of start position of each block in target.
)


# SMED blat files
smed_files <- dir(here("alignments"), pattern = "smed_20140614_.*.psl")
dfs <- lapply(smed_files, function(x) {
    read_tsv(here("alignments", x), skip=5,
             col_names = blat_cols
    ) %>%
        mutate(file = x)
    
})

smed_blat <- do.call(rbind, dfs)


smed_map <- smed_blat %>%
    mutate(transcriptome = str_replace(file, "smed_20140614_", "")) %>%
    mutate(transcriptome = str_replace(transcriptome, ".psl", "")) %>%          
    select(qName, tName, transcriptome) %>%
    distinct()

### SMEST blat files
smest_files <- dir(here("alignments"), pattern = "SMEST_dd_Smes_v2_.*.psl")
dfs <- lapply(smest_files, function(x) {
    read_tsv(here("alignments", x), skip=5,
             col_names = blat_cols
    ) %>%
        mutate(file = x)
    
})

smest_blat <- do.call(rbind, dfs)

smest_map <- smest_blat %>%
    mutate(transcriptome = str_replace(file, "SMEST_dd_Smes_v2_", "")) %>%
    mutate(transcriptome = str_replace(transcriptome, ".psl", "")) %>%          
    select(qName, tName, transcriptome) %>%
    distinct()


# join mapping files so we only have to add aliases once
# why not just output a joint file to begin with?
map <- rbind(smed_map, smest_map)

map <- map %>%
    rename(seq_id = qName) %>%
    rename(ref_id = tName) %>%
    rename(transcriptome_id = transcriptome) %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "original")

# split planmine_transcripts into individual transcriptomes
planmine_transcriptomes = c(
    "uc_Smed_v2",
    "mu_Smed_v1",
    "ox_Smed_v2",
    "be_Smed_v2",
    "bo_Smed_v1",
    "dd_Smed_v6",
    "dd_Smes_v1",
    "ka_Smed_v1",
    "to_Smed_v2"
)

planmine <- map %>%
    filter(transcriptome_id == "planmine_transcripts") %>%
    mutate(transcriptome = str_replace(seq_id, "^(.+?_.+?_.+?)_.+", "\\1")) %>%
    #mutate(transcriptome = str_replace(transcriptome, "SMEST.+", "SMEST")) %>%
    mutate(transcriptome_id = transcriptome) %>%
    select(-transcriptome)

map <- map %>%
    filter(transcriptome_id != "planmine_transcripts") 

map <- rbind(map, planmine) 

# ADD ALIASES
# remove version numbers from NCBI accessions
ncbi_no_version <- map %>%
    mutate(alias = seq_id) %>%
    mutate(alias = ifelse(transcriptome_id == "smed_ncbi_20200123",str_replace(alias, "\\.\\d+$",  "") , NA)) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "ncbi_drop_version_number")

# remove version numbers from db ests NCBI accessions
est_no_version <- map %>%
    mutate(alias = seq_id) %>%
    mutate(alias = ifelse(transcriptome_id == "ncbi_smed_ests",str_replace(alias, "\\.\\d+$",  "") , NA)) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "dbest_drop_version_number")

# sometimes ests are used by name instead of accession.  add names as alias
est2name <- read_tsv(here("ncbi_ests_to_names.txt"), col_names=c("seq_id", "alias")) 
est_old <- read_tsv(here("old_ests", "est2acc.txt")) 

est_ver <- est_old %>%
    mutate(seq_id = paste0(seq_id, ".1"))

est_old_plus_ver <- rbind(est_old, est_ver)

est_t3 <- est_old_plus_ver %>%
    mutate(alias = paste0(alias, "(T3)"))

est_joined_aliases <- rbind(est2name, est_old_plus_ver, est_t3)


est_combined <- est_joined_aliases %>%
    left_join(map) %>%
    mutate(seq_id = alias) %>%
    select(-alias) %>%
    filter(!is.na(ref_id)) %>%
    distinct() %>%
    mutate(alias_mod = "est_aliases")
                    


# the last number was cutoff some of the microarray probes when reporting in GPL14150
# in those cases we'llremove the version SMED_31964_V2_1 - SMED_31964_V2
GPL14150_no_version <- map %>%
    mutate(alias = seq_id) %>%
    mutate(alias = ifelse(transcriptome_id == "GPL14150",str_replace(alias, "(SMED_.+_V2)_\\d+",  "\\1") , NA)) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "remove_trailing_number")

# these probes have ids in format __nnn), one paper drops that version
GPL10652_no_version <- map %>%
    mutate(alias = seq_id) %>%
    mutate(alias = ifelse(transcriptome_id == "GPL10652",str_replace(alias, "(.+)__\\d+",  "\\1") , NA)) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "remove_trailing_number")

# convert oxford ids
oxford_reformat <- map %>%
    mutate(alias = seq_id) %>%
    filter(transcriptome_id == "ox_Smed_v2") %>%
    mutate(alias = str_replace(alias, "ox_Smed_v2_(\\d+)",  "OX_Smed_1.0.\\1")) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "remove_trailing_number")

# add ncbi names as aliases
ncbi_names <- read_tsv(here("ncbi_to_names.txt"), col_names=c("seq_id", "alias")) %>%
    left_join(map) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "ncbi_names")

# remove transcriptome name from newmark contigs
# uc_Smed_v2_Contig45882 = Contig45882
planmine_names <- map %>%
    filter(transcriptome_id %in% planmine_transcriptomes) %>%
    mutate(alias = seq_id) %>%
    mutate(alias = str_replace(alias, paste0(transcriptome_id, "_"),  "")) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "remove_transcriptome_name")

# rename muenster contigs
# mu_Smed_v1_11049_1_1 = tr5_11049
muenster_names <- map %>%
    filter(transcriptome_id == "mu_Smed_v1") %>%
    mutate(alias = seq_id) %>%
    mutate(alias = str_replace(alias, paste0(transcriptome_id, "_", "(\\d+)_.*"),  "tr5_\\1")) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id)%>%
    mutate(alias_mod = "rename_with_tr")

# truncate v6 and add as alias
# dd_Smed_v6_6243_0_2 = dd_Smed_v6_6243_0
# I think they did this to collapse isoforms - pub 29674432
plass <- map %>%
    mutate(alias = seq_id) %>%
    filter(transcriptome_id == "dd_Smed_v6") %>%
    mutate(alias = str_replace(alias, "(dd_Smed_v6_\\d+_\\d+)_\\d+",  "\\1")) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "drop_last_number")

# truncate dd_v4 alias
# dd_Smed_v4_2920_0_1 = dd_2920  (why did they do this?)
dd4_trunc <- map %>%
    filter(transcriptome_id == "dd_Smed_v4") %>%
    mutate(alias = seq_id) %>%
    mutate(alias = str_replace(alias, "dd_Smed_v4_(\\d+)_\\d+_\\d+",  "dd_\\1")) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "drop_last_two_blocks")

# some dd_v4 names end with _0_1 which is not in the file.  
# These are  isoforms that didn't make it into my version
# as we dont' really ahve isoform resolution we'll just add them

dd4_iso1 <- map %>%
    mutate(alias = seq_id) %>%
    filter(transcriptome_id == "dd_Smed_v4") %>%
    mutate(alias = str_replace(alias, "dd_Smed_v4_(\\d+)_\\d+_\\d+",  "dd_\\1_0_1")) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "add_isoform_1")

# truncate array names
# I'm not sure i know where hte sequences for some of hte newmark arrasy came from,
# but we mapped probes
# convert probe to seqname
# Contig1645_SE3  and Contig1645_338_372 = Contig1645
array_trunc <- map %>%
    mutate(alias = seq_id) %>%
    filter(transcriptome_id == "GPL15192") %>%
    mutate(alias = str_replace(alias, "(Contig\\d+?)_.+",  "\\1")) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "convert_probe_to_seq_name")

# SMU
# SMUs are mapped to dd_v4
smu2dd4 <- read_tsv(here("SMU_to_dd4.txt"), col_names=c("seq_id", "alias"))
smu <- map %>%
    left_join(smu2dd4) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id) %>%
    mutate(transcriptome_id = "SMU") %>%
    mutate(alias_mod = "dd4_to_SMU")

# for NCBI proteins we don't want to map with aa seqs, so we use the accession
# of hte corresponding nucleotide sequence
# we need aliases with protein accession, protein accession w/o verion and protein name.

ncbiprot2nucl <- read_tsv(here("ncbiprot_to_ncbinucl.txt"))
ncbi_prot <- ncbiprot2nucl %>%
    rename(seq_id = nuc_id) %>%
    rename(alias = accession) %>%
    left_join(map) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "original")

ncbi_prot_with_1 <- ncbiprot2nucl %>%
    rename(seq_id = nuc_id) %>%
    rename(alias = accession) %>%
    left_join(map) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(seq_id = paste0(seq_id, ".1")) %>%
    mutate(alias_mod = "add_version_num")

ncbi_prot_name <- ncbiprot2nucl %>%
    rename(seq_id = nuc_id) %>%
    rename(alias = definition) %>%
    left_join(map) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "ncbi definition")

ncbi_with_smed <- ncbiprot2nucl %>%
    rename(seq_id = nuc_id) %>%
    rename(alias = definition) %>%
    mutate(alias = paste0("Smed-", alias)) %>%
    left_join(map) %>%
    select(-seq_id) %>%
    rename(seq_id = alias) %>%
    filter(!is.na(seq_id)) %>%
    distinct() %>%
    select(seq_id,ref_id,transcriptome_id) %>%
    mutate(alias_mod = "add_Smed-")

map_with_aliases <- rbind(map, 
                          ncbi_no_version,
                          est_no_version,
                          est_combined,
                          GPL14150_no_version,
                          GPL10652_no_version,
                          oxford_reformat,
                          ncbi_names,
                          planmine_names,
                          muenster_names,
                          plass,
                          dd4_trunc,
                          dd4_iso1,
                          array_trunc,
                          smu,
                          ncbi_prot,
                          ncbi_prot_with_1,
                          ncbi_prot_name,
                          ncbi_with_smed
                          ) %>%
    filter(! is.na(transcriptome_id))


# add smesg mappings 
smest2smesg <- read_tsv(here("smes_v2_hconf_smest2smesg.map"), col_names=c("smest_id", "smesg_id"))

tran2smesg <- map_with_aliases %>%
    left_join(smest2smesg, by=c("ref_id" = "smest_id")) %>%
    filter(!is.na(smesg_id)) %>%
    select(-ref_id) %>%
    rename(ref_id = smesg_id)

# add smed3 to smesg entries
smed2smesg <- map_with_aliases %>%
    filter(str_detect(ref_id, "SMED3")) %>%
    left_join(smest2smesg, by=c("seq_id" = "smest_id")) %>%
    filter(!is.na(smesg_id)) %>%
    select(-seq_id) %>%
    rename(seq_id = smesg_id) %>%
    mutate(transcriptome_id = "SMESG_dd_Smes_v2") %>%
    mutate(alias_mod = "original") %>%
    distinct()
    
tran2smed3 <- map_with_aliases %>%
    filter(str_detect(ref_id, "SMED3")) %>%
    rbind(smed2smesg)

# add smest to smesg entries to mappign table
smestmap2smesg <- map_with_aliases %>%
    filter(str_detect(ref_id, "SMEST")) %>%
    left_join(smest2smesg, by=c("seq_id" = "smest_id")) %>%
    filter(!is.na(smesg_id)) %>%
    select(-seq_id) %>%
    rename(seq_id = smesg_id) %>%
    mutate(transcriptome_id = "SMESG_dd_Smes_v2") %>%
    mutate(alias_mod = "original") %>%
    distinct()

tran2smest <- map_with_aliases %>%
    filter(str_detect(ref_id, "SMEST")) %>%
    rbind(smestmap2smesg)

# OUTPUT
# we want 3 output files
# 1- transcripts to smed_201401614 (tran2smed3)
# 2- transcripts to SMEST (tran2smest)
# 3- transcripts to SMESG (tran2smesg)


write_tsv(tran2smed3, here("OUTPUT", "smed_201401614_transcriptome_mapping.txt"))
write_tsv(tran2smest, here("OUTPUT", "smest_transcriptome_mapping.txt"))
write_tsv(tran2smesg, here("OUTPUT", "smesg_transcriptome_mapping.txt"))
