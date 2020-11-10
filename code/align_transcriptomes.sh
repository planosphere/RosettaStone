#!/usr/bin/bash
# align transcriptomes and microarray probes to smed and SMEST references


# set DIR to directory containing transcriptome fasta files and
# a file containing a list of transcriptome files to use: transcriptoms.list
# and a file containing a list of microarry probe files to use: microarrays.list

# output will be in subdirectory of DIR: "alignments"



DIR=`pwd`
DB="smed_20140614.nt"
PBLAT="/home/ejr/src/pblat/pblat"


for QUERY in `cat ${DIR}/transcriptomes.list`
do

${PBLAT} \
-threads=24 \
-minScore=100 \
-minIdentity=95 \
${DIR}/${DB} \
${DIR}/${QUERY} \
${DIR}/alignments/${DB%.nt}_${QUERY%.nt}.psl


done


for QUERY in `cat ${DIR}/microarrays.list`
do

${PBLAT} \
-threads=24 \
-minScore=30 \
-minIdentity=95 \
${DIR}/${DB} \
${DIR}/${QUERY} \
${DIR}/alignments/${DB%.nt}_${QUERY%.nt}.psl
done

### SMEST

DB="SMEST_dd_Smes_v2.nt"


for QUERY in `cat ${DIR}/transcriptomes.list`
do

${PBLAT} \
-threads=24 \
-minScore=100 \
-minIdentity=95 \
${DIR}/${DB} \
${DIR}/${QUERY} \
${DIR}/alignments/${DB%.nt}_${QUERY%.nt}.psl


done


for QUERY in `cat ${DIR}/microarrays.list`
do

${PBLAT} \
-threads=24 \
-minScore=30 \
-minIdentity=95 \
${DIR}/${DB} \
${DIR}/${QUERY} \
${DIR}/${DB%.nt}_${QUERY%.nt}.psl


done
