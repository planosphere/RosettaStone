<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>PAGE</title>

<style type="text/css">
 .node-title {
    display: none;
}
#outer
{
     width:50%;
    text-align: left;
}
.inner
{
    display: inline-block;
}
.first-column {
    width: 66%;
    padding: 0 10px 0 0;
    float: left;
}

.second-column {
    width: 33%;
    padding: 0 10px 0 0;
    float: right;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/0.11.1/typeahead.bundle.min.js"></script>
<script src="/pub/OLS/OLS-autocomplete/build/ols-autocomplete.js"></script>


</head>
<body>
<div id="searchform" class="row">
<div class="first-column">
<h2 style="color:#32821f;font-size:1.9em;font-family:oswaldregular">Quickly map IDs to a reference sequence and other transcriptome IDs.</h2> <hr>



<?php

$c1 = '
<div style="position: relative;left: 10px;"></div>
<div style="font-size:1.2em;">
<br>
<ul>
<li>All sequences in each included transcriptome database have been mapped to each of the three reference databases listed in the last drop-down (smed_2010614, SMEST, SMESG).</li>

<li>How Rosetta Stone Transcript Mapper Works:
 <ol style="position: relative;left: 10px;">
   <li>Starts with the IDs you input into the text box (in results: "User Accession" column).</li>
   <li>Locates the transcriptome your input IDs originated from (in results: "User Accession DB" column).</li>
   <li>Locates the reference sequences that map to your input IDs. (in results: "Reference Sequence" column).</li>   
   <li>Identifies all other IDs that also map to this reference sequence (in results: "Homolog Accession" column).</li>
   <li>Results are limited to transcriptome databases you select in the drop-down (in results: "Homolog Accession DB" column).</li>
</ol></li>
<li>SMEST and SMESG descriptions where downloaded from Planmine</li>
<li>Last Update May 2020</li>
</ul>
</div>

';
print theme(
  'ctools_collapsible',
  array(
    'handle' => '<div style="font-size:1.2em">Click to for more information about this Rosetta Stone Transcript Mapper Search</div>',
    'content' => $c1,
    'collapsed' => TRUE
  )
);



print("<p></p>");

?>





<hr>
<div class="grid_10 omega" style="margin-left:30px">
    <style scoped>
        @import "/pub/OLS/OLS-autocomplete/css/ols-colors.css";
        @import "/pub/OLS/OLS-autocomplete/css/ols.css";
        @import "/pub/OLS/OLS-autocomplete/css/bootstrap.css";
        @import "/pub/OLS/OLS-autocomplete/css/typeaheadjs.css";

    </style>




                <form method="post">  
                    <fieldset>


                        <label style="font-size:1.2em;">Select a reference database: <br>
<select name="ref_seq_type" id="ref_seq_type">
<option value='smed_20140614'>Sánchez Alvarado lab, smed_20140614, 2014 (Planosphere) (smed_20140614)</option>
<option value='SMESG_dd_Smes_v2'>Rink lab, SMESG_dd_Smes_v2 (Planmine) (SMESG_dd_Smes_v2)</option>
<option value='SMEST_dd_Smes_v2'>Rink lab, SMEST_dd_Smes_v2 (Planmine) (SMEST_dd_Smes_v2)</option>
</select><div style="font-family=Oswald;font-weight: 300;font-style: normal;">Input IDs and transcriptome DBs are mapped to the selected reference</div><br><br>
                        </label>


                        <label style="font-size:1.2em;">Enter one or more transcript IDs that you want translated to another ID (separated by whitespace):<br>
 <textarea style="font-weight: normal" rows="4" cols="50"  name="q" ></textarea>
<div style="font-family=Oswald;font-weight: 300;font-style: normal;">Example search: SmWIOct06_100018 JQ425143 SMED30008505 dd_Smed_v4_1757_0_1 dd_Smed_v6_7985_0</div><br>
                        </label>

                        <label style="font-size:1.2em;">Select one or more transcriptome DBs that you want your IDs from above translated into (displayed as homolog in the resulting table):<br>


<select name="db_ids[]" id="db_ids" multiple>
<option value='ox_Smed_v2'>Aboobaker lab, Kao V5, 2013 (Planmine)(ox_Smed_v2)</option>
<option value='mu_Smed_v1'>Bartscherer lab, Sandmann et al., 2011 (Planmine)(mu_Smed_v1)</option>
<option value='ncbi_smed_ests'>NCBI Smed ESTs(ncbi_smed_ests)</option>
<option value='smed_ncbi_20200123'>NCBI NUCCORE(smed_ncbi_20200123)</option>
<option value='uc_Smed_v2'>Newmark lab, Rouhana et al., 2012 (Planmine) (uc_Smed_v2)</option>
<option value='newmark_ests'>Newmark lab, Rouhana et al., 2012 (newmark_ests)</option>
<option value='GPL15192'>Newmark lab, Smed1 CombiMatrix array, Forsthoefel et al., 2012 (GPL15192)</option>
<option value='GPL15193'>Newmark lab, Smed2 CombiMatrix array, Forsthoefel et al., 2012 (GPL15193)</option>
<option value='GPL10652'>Newmark lab, NimbleGen_Schmidtea mediterranea_385k array, 2010 (GPL10652)</option>
<option value='SmedAsxl_pearson_GAKN01.1'>Pearson lab, Currie et al., 2013 (SmedAsxl_pearson_GAKN01.1)</option>
<option value='to_Smed_v2'>Pearson lab, Labbé et al., 2012 (Planmine) (to_Smed_v2)</option>
<option value='be_Smed_v2'>Rajewsky lab, Adamidi et al., 2011 (Planmine)(be_Smed_v2)</option>
<option value='bo_Smed_v1'>Reddien lab, Srivastava et al., 2014 (Planmine) (bo_Smed_v1)</option>
<option value='GPL14150'>Reddien Lab, microarray, Wenermoser et al., 2012 (GPL14150)</option>
<option value='GPL14150_gene_models'>Reddien Lab, gene models, Wenermoser et al., 2012(GPL14150_gene_models)</option>
<option value='SMESG_dd_Smes_v2'>Rink lab, SMESG_dd_Smes_v2 (Planmine) (SMESG_dd_Smes_v2)</option>
<option value='SMEST_dd_Smes_v2'>Rink lab, SMEST_dd_Smes_v2 (Planmine) (SMEST_dd_Smes_v2)</option>
<option value='dd_Smed_v6'>Rink lab, Dresden V6 (Planmine) (dd_Smed_v6)</option>
<option value='dd_Smed_v4'>Rink lab, Dresden V4 (Planmine) (dd_Smed_v4)</option>
<option value='dd_Smes_v1'>Rink lab, SMES V1 (Planmine) (dd_Smes_v1)</option>
<option value='SmedSxl_ww_GDAG01.fsa_nt'>Sánchez Alvarado lab, SmedSxl_ww_GDAG01, 2015 (SmedSxl_ww_GDAG01.fsa_nt)</option>
<option value='SmedAsxl_ww_GCZZ01'>Sánchez Alvarado lab, SmedAsxl_ww_GCZZ01, 2015 (SmedAsxl_ww_GCZZ01)</option>
<option value='SMU'>Sánchez Alvarado lab, Unigenes, 2015 (SMU)</option>
<option value='smed_20140614'>Sánchez Alvarado lab, smed_20140614, 2014 (smed_20140614)</option>
<option value='ka_Smed_v1'>Sánchez Alvarado, PRJNA215411, 2013 (Planmine) (ka_Smed_v1)</option>


</select>
                <div style="font-family=Oswald;font-weight: 300;font-style: normal;">To select more than one use command or ctrl. <br>Example: Select 'to_Smed_v2' and 'ox_Smed_v2' and 'SMEST_dd_Smes_v2'</div></label>


                    
<div>
   <br>

                     <input type="submit" value="Search" class="submit"></input> 
</div>
                  </fieldset>
                </form>

</div>

</div>
<div class="second-column">
<div class="block-inner clearfix">
            
    <div class="block-content clearfix">



    </div>
  </div>
</div>

</div> <!-- end of searchfomr -->


<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

if (empty($_POST["q"])){

  print '<div style="color:red;"><p>Please input transcriptome IDs to be mapped to a reference and other transcriptome databases</p></div>';
}
elseif( empty ($_POST["db_ids"])){

  print '<div style="color:red;"><p>Please select at least one transcriptome DB to be mapped to your input IDs</p></div>';

}else{

$q = $_POST["q"];
$transcripts = trim($transcripts);
$transcripts = preg_split('/[\s+\n\,]+/', $q);
$these_transcripts = " VALUES ?accession { '". implode("'^^string: '", $transcripts) . "'^^string:} ";

$db_ids=$_POST["db_ids"];

//$db_ids_split = preg_split('/[\s+\n\,]+/', $db_ids);
$these_dbs = "VALUES ?source2 { '". implode("'^^string: '", $db_ids) . "'^^string:}";

$ref_seq_type = $_POST["ref_seq_type"];



$query = " PREFIX string: <http://www.w3.org/2001/XMLSchema#string> 
PREFIX oban: <http://oban.org/oban/> 
PREFIX PAGE: <http://planosphere.stowers.org/page/> 
PREFIX dc: <http://purl.org/dc/elements/1.1/> 
PREFIX description: <http://purl.obolibrary.org/obo/IAO_0000115> 
SELECT DISTINCT  ?ref_seq ?accession ?source ?accession2 ?source2 ?description
WHERE {  
?a1 oban:association_has_predicate PAGE:has_mapping_reference_id ; 
  oban:association_has_subject ?accession ; 
  oban:association_has_object ?ref_seq ; 
  PAGE:has_reference_source '$ref_seq_type'^^string: ; 
  dc:source ?source . 
$these_transcripts . 
?a2 oban:association_has_predicate PAGE:has_mapping_reference_id ; 
  oban:association_has_subject ?accession2 ; 
  oban:association_has_object ?ref_seq ; 
  PAGE:has_reference_source '$ref_seq_type'^^string: ; 
  dc:source ?source2 . 
$these_dbs .
OPTIONAL {
    ?a3 oban:association_has_object_property description: ;
     oban:association_has_subject ?ref_seq ; 
     oban:association_has_object ?description ; 
 }
}  ";

$sparql = array(
        'query' => $query,
        'format' => 'json'
);
$url = "http://172.16.2.41:8889/page/sparql";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sparql));

$expand_json = curl_exec($ch);
curl_close ($ch);
$expand= json_decode($expand_json,true);

$ref_urls = [];
$ref_urls['SMEST_dd_Smes_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$ref_urls['smed_20140614']='https://planosphere.stowers.org/feature/Schmitea/mediterranea-sexual/transcript/';
$ref_urls['SMESG_dd_Smes_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?externalids=';

$ref_url = $ref_urls[$ref_seq_type];

//url source for dbs
$db_url=[];
$db_url['be_Smed_v2']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['bo_Smed_v1']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['dd_Smed_v4']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['dd_Smed_v6']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['dd_Smes_v1']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['GPL10652']='https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GPL10652';
$db_url['GPL14150']='https://www.ncbi.nlm.nih.gov/pmc/articles/PMC3347795/';
$db_url['GPL14150_gene_models']='https://www.ncbi.nlm.nih.gov/pmc/articles/PMC3347795/';
$db_url['GPL15192']='https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE35565';
$db_url['GPL15193']='https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE35565';
$db_url['ka_Smed_v1']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['mu_Smed_v1']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['ncbi_smed_ests']='https://www.ncbi.nlm.nih.gov/nuccore/';
$db_url['newmark_ests']='https://ftp.ncbi.nlm.nih.gov/geo/platforms/GPL10nnn/GPL10652/suppl/GPL10652_2007-11-06_Smed_ESTs_4_expr.ndf.gz';
$db_url['ox_Smed_v2']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['smed_20140614']='http://smedgd.stowers.org/files/smed_20140614.nt.gz';
$db_url['SmedAsxl_pearson_GAKN01.1']='https://www.ncbi.nlm.nih.gov/nuccore/GAKN00000000';
$db_url['SmedAsxl_ww_GCZZ01']='https://www.ncbi.nlm.nih.gov/nuccore/GCZZ00000000';
$db_url['smed_ncbi_20200123']='https://www.ncbi.nlm.nih.gov/nuccore/';
$db_url['SmedSxl_ww_GDAG01.fsa_nt']='https://www.ncbi.nlm.nih.gov/nuccore/GDAG00000000';
$db_url['SMESG_dd_Smes_v2']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['SMEST_dd_Smes_v2']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['SMU']='http://smedgd.stowers.org/files/Smed_unigenes_20150217.aa.gz';
$db_url['to_Smed_v2']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';
$db_url['uc_Smed_v2']='http://planmine.mpi-cbg.de/planmine/aspect.do?name=Transcriptomes';

$acc_url=[];
$acc_url['be_Smed_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['bo_Smed_v1']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['dd_Smed_v4']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['dd_Smed_v6']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['dd_Smes_v1']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['ka_Smed_v1']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['mu_Smed_v1']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['ncbi_smed_ests']='https://www.ncbi.nlm.nih.gov/nuccore/';
$acc_url['ox_Smed_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['smed_20140614']='https://planosphere.stowers.org/feature/Schmitea/mediterranea-sexual/transcript/';
$acc_url['SmedAsxl_pearson_GAKN01.1']='https://www.ncbi.nlm.nih.gov/nuccore/';
$acc_url['SmedAsxl_ww_GCZZ01']='https://www.ncbi.nlm.nih.gov/nuccore/';
$acc_url['smed_ncbi_20200123']='https://www.ncbi.nlm.nih.gov/nuccore/';
$acc_url['SmedSxl_ww_GDAG01.fsa_nt']='https://www.ncbi.nlm.nih.gov/nuccore/';
$acc_url['SMESG_dd_Smes_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?externalids=';
$acc_url['SMEST_dd_Smes_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['SMU']='http://smedgd.stowers.org/cgi-bin/genePage.pl?ref=';
$acc_url['to_Smed_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';
$acc_url['uc_Smed_v2']='http://planmine.mpi-cbg.de/planmine/portal.do?class=Contig&externalids=';


$to_print = "<div style=\"overflow-x:auto;\"><table>";
$to_print .=  "<thead><tr><th>Reference Sequence</th><th>Description</th><th>User Accession</th><th>User Accession DB</th><th>Homolog Accession</th><th>Homolog Accession DB</th></tr></thead>";
$to_print .= "<tbody>";

$result_count = 0;

foreach ($expand as $result){
 $r = $result['bindings'];
 foreach($r as $each){
   $result_count++;
   $ref_seq = $each['ref_seq']['value'];
   $seqs = explode(';',$ref_seq);
   $description = $each['description']['value'];
   $q_accession = $each['accession']['value'];
   $q_accession_source =  $each['source']['value'];
   $homolog_accession =  $each['accession2']['value'];
   $homolog_accession_source = $each['source2']['value'];
   $q_accession_link = $q_accession;
   $homolog_accession_link = $homolog_accession;
   $q_accession_source_link = $q_accession_source;
   $homolog_accession_source_link = $homolog_accession_source;
   if (array_key_exists($q_accession_source,$acc_url)){
     $q_accession_link = "<a href=\"$acc_url[$q_accession_source]$q_accession\" target=_blank>$q_accession</a>";
     $q_accession_source_link = "<a href=\"$db_url[$q_accession_source]\" target=_blank>$q_accession_source</a>";
   }
   if (array_key_exists($homolog_accession_source,$acc_url)){
     $homolog_accession_link = "<a href=\"$acc_url[$homolog_accession_source]$homolog_accession\" target=_blank>$homolog_accession</a>";
     $homolog_accession_source_link = "<a href=\"$db_url[$homolog_accession_source]\" target=_blank>$homolog_accession_source</a>";
   }

   foreach ($seqs as $seq){
     $ref_seq_url="<a target=_blank href=\"$ref_url$seq\">$seq</a>";
     $to_print .= "<tr><td>$ref_seq_url</td><td>$description</td><td>$q_accession_link</td><td>$q_accession_source_link</td><td>$homolog_accession_link</td><td>$homolog_accession_source_link</td></tr>";
   }
 }
}

$to_print .= "</tbody>";
$to_print .= "</table></div>" ;


print "<div>Result Count: $result_count<div>";
if($result_count > 0){

  print "<div class=\"views-table\">
<div id=\"outer\">

<div class=\"inner\">
<form action=\"/search/rosettastone/download\" method=\"post\">
  <input type=\"hidden\" name=\"sparql\" value=\"$query\">
  <input type=\"submit\" value=\"Download Results\">
</form>
</div>

<div class=\"inner\">
<form action=\"/download/sparql\" method=\"post\">
  <input type=\"hidden\" name=\"sparql\" value=\"$expand_query\">
  <input type=\"submit\" value=\"Download SPARQL Query\">
</form>
</div>
</div>
<hr>
$to_print
</div>
";
}




}
}
?>

</body>
</html>


