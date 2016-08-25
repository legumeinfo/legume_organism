<?php

$organism  = $variables['node']->organism;
//$organism = chado_expand_var($organism,'table','genome_metadata');

$organism = chado_expand_var($organism  , 'table', 'stock');
//drupal_set_message('resws: <pre>' . print_r($organism->stock, true) . '</pre>');
$stocks = $organism->stock;
//echo "STOCKS: <pre>";var_dump($stocks);echo "</pre>";
foreach ($stocks as $stock) {
  $stock = chado_expand_var($stock, 'table', 'genome_metadata');
  $stock = chado_expand_var($stock, 'table', 'projectprop');
  $stock = chado_expand_var($stock, 'table', 'project_dbxref');
  $stock = chado_expand_var($stock, 'table', 'nd_experimentprop');
  $stock = chado_expand_var($stock, 'table', 'nd_experiment_dbxref');
  $stock = chado_expand_var($stock, 'table', 'stockprop');
  $stock = chado_expand_var($stock, 'table', 'stock_dbxref');
  $stock = chado_expand_var($stock, 'table', 'nd_geolocation');
  $stock = chado_expand_var($stock, 'table', 'analysisprop');
   
  if ($stock->genome_metadata) 
  {
     $genomemetadata = $stock->genome_metadata;
//echo "GENOME METADATA: <pre>";var_dump($genomemetadata);echo "</pre>";
//eksc- what is this? never used
//     $x=$stock;
  }
}
//echo "ENTIRE VARIABLE: <pre>";var_dump($organism);echo "</pre>";

if (!$genomemetadata) {
  // Nothing to see here
  return;
}
?>

<div class="tripal_organism-data-block-desc tripal-data-block-desc"></div><?php

// the $headers array is an array of fields to use as the colum headers. 
// additional documentation can be found here 
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
// This table for the organism has a vertical header (down the first column)
// so we do not provide headers here, but specify them in the $rows array below.
$headers = array();

// the $rows array contains an array of rows where each row is an array
// of values for each column of the table in that row.  Additional documentation
// can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7 
$rows = array();
$rowsAnalysis = array();
$SampleData = array();
//eksc- use top section for project information
//$ProjectData = array();


///////////////////////////////////////////////////////////////////////////////
// PROJECT INFORMATION
///////////////////////////////////////////////////////////////////////////////

/*eksc- not needed
//Project id row
$rows[] = array(
  array(
    'data' => 'Project Id', 
    'header' => FALSE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->project_id->project_id . '</i>'
);
 $proj= db_query(
    'SELECT * FROM public.chado_project WHERE project_id LIKE :project_id',
    array(':project_id' => 104)
  )->fetchAll();
*/

// Project Name row
$rows[] = array(
  array(
    'data' => 'Project Name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<a href="/node/'.$genomemetadata->project_nid.'">' . $genomemetadata->project_id->name . '</a>'
);

// Project description
$genomemetadata = chado_expand_var($genomemetadata, 'field', 'genome_metadata.project_description');
$rows[] = array(
  array(
    'data' => 'Project Description', 
    'header' => TRUE,
    'width' => '20%',
  ),
  $genomemetadata->project_description,
);

// Consortium, if any
if ($genomemetadata->consortium) {
  $consortium = ($genomemetadata->consortium_url)
              ? l($genomemetadata->consortium, $genomemetadata->consortium_url) 
              : $genomemetadata->consortium;
  $rows[] = array(
    array(
      'data' => 'Consortium', 
      'header' => TRUE,
      'width' => '20%',
    ),
    $consortium,
  );
}

// BioProject accession, if any
if ($genomemetadata->bioproject) {
  $bioproject_db = chado_generate_var('db', array('db_id' => $genomemetadata->bioproject_db));
  $bioproject = l($genomemetadata->bioproject, $bioproject_db->urlprefix.$genomemetadata->bioproject);
  $rows[] = array(
    array(
      'data' => 'BioProject', 
      'header' => TRUE,
      'width' => '20%',
    ),
    $bioproject
  );
}

foreach($genomemetadata->project_id->projectprop as $value) {
  // Control which properties are displayed
  switch ($value->type_id->name) {
    case 'funding':
      $rows[] = array(
        array(
         'data' => 'Funding' , 
         'header' => TRUE,
         'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'project_PI':
      $rows[] = array(
        array(
          'data' => 'Project lead PI' , 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'release_date':
      $rows[] = array(
        array(
          'data' => 'Release Date' , 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'contributors':
      // Expand long text field
      $value = chado_expand_var($value, 'field', 'projectprop.value');
      $rows[] = array(
        array(
          'data' => 'Contributors' , 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
  }
}//each projectprop

// publication, if any
if ($genomemetadata->doi) {
  $pub_db = chado_generate_var('db', array('name' => 'DOI'));
  $pub_identifier = l($genomemetadata->doi, $pub_db->urlprefix.$genomemetadata->doi);
}
else if ($genomemetadata->pmid) {
  $pub_db = chado_generate_var('db', array('name' => 'PMID'));
  $pub_identifier = l($genomemetadata->pmid, $pub_db->urlprefix.$genomemetadata->pmid);
}
else {
  $pub_identifier = "unknown";
}
$rows[] = array(
  array(
    'data' => 'Publication' , 
    'header' => TRUE,
    'width' => '20%',
  ),
  $pub_identifier,
);

  


///////////////////////////////////////////////////////////////////////////////
// SAMPLE INFORMATION
///////////////////////////////////////////////////////////////////////////////

// Stock name
$url = "/node/";
$stock = ($genomemetadata->stock_nid)
       ? l($genomemetadata->stock_name, $url.$genomemetadata->stock_nid) 
       : $genomemetadata->stock_name;

$SampleData[] = array(
  array(
    'data' => 'Stock Name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  $stock,
);

//echo "<pre>";var_dump($genomemetadata);echo "</pre>";
// Sample description
$genomemetadata - chado_expand_var($genomemetadata, 'field', 'genome_metadata.sample_description');
$SampleData[] = array(
  array(
    'data' => 'Sample Description', 
    'header' => TRUE,
    'width' => '20%',
  ),
  $genomemetadata->sample_description,
);

// Location
$SampleData[] = array(
  array(
    'data' => 'Location', 
    'header' => TRUE,
    'width' => '20%',
  ),
  $genomemetadata->nd_experiment_id->nd_geolocation_id->description
);

// BioSample, if any
if ($genomemetadata->biosample) {
  $biosample_db = chado_generate_var('db', array('db_id' => $genomemetadata->biosample_db));
//echo "<pre>";var_dump($biosample_db);echo "</pre>";
  $biosample = l($genomemetadata->biosample, $biosample_db->urlprefix.$genomemetadata->biosample);
  $SampleData[] = array(
    array(
      'data' => 'BioSample', 
      'header' => TRUE,
      'width' => '20%',
    ),
    $biosample
  );
}

foreach ($genomemetadata->nd_experiment_id->nd_experimentprop as $value) {
  // Control which properties are displayed
  switch ($value->type_id->name) {
    case 'collected_by':
      $SampleData[] = array(
        array(
          'data' => 'Collected by', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'collection_date':
      $SampleData[] = array(
        array(
          'data' => 'Collection Date', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'biomaterial_provider':
      $SampleData[] = array(
        array(
          'data' => 'Provided by', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
  }
}//each experimentprop

/*eksc- not needed
//analysis
$rows[] = array(
  array(
    'data' => 'Analysis Id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->analysis_id->analysis_id . '</i>'
);
*/

/*eksc- not needed
$rows[] = array(
  array(
    'data' => 'Executed Time', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->analysis_id->timeexecuted . '</i>'
);
*/

/*eksc- not needed
//experiment id
$rows[] = array(
  array(
    'data' => 'Experiment Id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->nd_experiment_id . '</i>'
);
*/

//location

/*eksc- not needed
$rows[] = array(
  array(
    'data' => 'Location Id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->nd_geolocation_id->nd_geolocation_id . '</i>'
);
*/

/*eksc- not needed
//type_id
$rows[] = array(
  array(
    'data' => 'type name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->name . '</i>'
);
*/

/*eksc- not needed
$rows[] = array(
  array(
    'data' => 'type definition', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->definition . '</i>'
);
*/

/*eksc- not needed

//dbxref
$rows[] = array(
  array(
    'data' => 'dbxref_id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->dbxref_id->dbxref_id . '</i>'
);
*/

/*eksc- not needed
$rows[] = array(
  array(
    'data' => 'db id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->dbxref_id->db_id->db_id . '</i>'
);
*/

/*eksc- not needed
$rows[] = array(
  array(
    'data' => 'db name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->dbxref_id->db_id->name . '</i>'
);
*/

//select * from public.chado_stock where stock_id=;
//  $results= db_query(
//     'SELECT * FROM public.chado_stock WHERE stock_id LIKE :stock_id',
//     array(':stock_id' => $genomemetadata->stock_id->stock_id)
//   )->fetchAll();
//   $sql = "SELECT * FROM chado_stock WHERE stock_id = :stock_id ";
//     $results = db_query($sql, array(':stock_id' => 205))->fetchFeild;
//     drupal_set_message("where am i");
  //   drupal_set_message('results: <pre>' . print_r($results, true) . '</pre>');
//     drupal_set_message($results[0]->nid);
        // stock
// $rows[] = array(
//   array(
//     'data' => 'stock id', 
//     'header' => TRUE,
//     'width' => '20%',
//   ),
//   '<i>' . $genomemetadata->stock_id->stock_id . '</i>'
// );

// 
// $rows[] = array(
//   array(
//     'data' => 'Project Data', 
//     'header' => TRUE,
//     'width' => '30%',
//   )
//  
// );
// 
// $rows[] = array(
//   array(
//     'data' => 'BioProject', 
//     'header' => TRUE,
//     'width' => '20%',
//   )
//  
// );

// $rows[] = array(
//   array(
//     'data' => $genomemetadata->project_id->projectprop[9]->type_id->name , 
//     'header' => TRUE,
//     'width' => '20%',
//   ),
//   '<i>' . $genomemetadata->project_id->projectprop[9]->value . '</i>'
// );
// drupal_set_message($genomemetadata->project_id->projectprop.length);
// for($i=0;$i<$genomemetadata->project_id->projectprop.length;$i++)
// {
// drupal_set_message($i);
// }

// $SampleData[] = array(
//  array(
//     'data' => "Sample Data" , 
//     'header' => TRUE,
//     'width' => '20%',
//   ) 
// );

// $rows[] = array(
//  array(
//     'data' => "Assembly" , 
//     'header' => TRUE,
//     'width' => '20%',
//   ) 
// );



///////////////////////////////////////////////////////////////////////////////
// ASSEMBLY INFORMATION
///////////////////////////////////////////////////////////////////////////////

// Assembly name
$rowsAnalysis[] = array(
  array(
    'data' => 'Assembly Name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  $genomemetadata->assembly_name,
);

/*eksc- need to think how to handle this as this field is not currently 
        collected, but is useful
// Assembly description
$genomemetadata - chado_expand_var($genomemetadata, 'field', 'genome_metadata.assembly_description');
$rowsAnalysis[] = array(
  array(
    'data' => 'Assembly Description', 
    'header' => TRUE,
    'width' => '20%',
  ),
  $genomemetadata->assembly_description,
);
*/

//$genomemetadata = chado_expand_var($genomemetadata->analysis_id,'table','cvterm');
foreach($genomemetadata->analysis_id->analysisprop as $value) {
  // Control which properties are displayed
  switch ($value->type_id->name) {
    case 'seq_meth':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Sequence Method', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'genome_coverage':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Genome Coverage', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'sequencing_technologies':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Sequencing Technology', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'seq_service_provider':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Sequencing Service', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'finishing_strategy':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Finishing Strategy', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'assembly_size':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Assembly Size', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'scaffold_genome_coverage':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Percent genome covered by scaffolds', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
    case 'scaff_num':
      $rowsAnalysis[] = array(      
        array(
          'data' => 'Number of scaffolds', 
          'header' => TRUE,
          'width' => '20%',
        ),
        $value->value,
      );
      break;
  }

}


// $rows[] = array(
//   array(
//     'data' => 'stock unique name', 
//     'header' => TRUE,
//     'width' => '20%',
//   ),
//   '<i>' . $genomemetadata->stock_id->uniquename . '</i>'
// );

// $rows[] = array(
//   array(
//     'data' => 'Organism Id', 
//     'header' => TRUE,
//     'width' => '20%',
//   ),
//   '<i>' . $genomemetadata->stock_id->organism_id->organism_id . '</i>'
// );
// 
// $rows[] = array(
//   array(
//     'data' => 'Genus', 
//     'header' => TRUE
//   ), 
//   '<i>' . $genomemetadata->stock_id->organism_id->genus . '</i>'
// );
// 
// 
// 
// // species row
// $rows[] = array(
//   array(
//     'data' => 'Species', 
//     'header' => TRUE
//   ), 
//   '<i>' .  $genomemetadata->stock_id->organism_id->species . '</i>'
// );
// 
// // common name row
// $rows[] = array(
//   array(
//     'data' => 'Common Name',
//     'header' => TRUE
//   ),
//    $genomemetadata->stock_id->organism_id->common_name,
// );
// 
// // abbreviation row
// $rows[] = array(
//   array(
//     'data' => 'Abbreviation', 
//     'header' => TRUE
//   ),
//    $genomemetadata->stock_id->organism_id->abbreviation
// );

// the $table array contains the headers and rows array as well as other
// options for controlling the display of the table.  Additional 
// documentation can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
$table = array(
  'header' => $headers, 
  'rows' => $rows, 
  'attributes' => array(
    'id' => 'tripal_organism-table-base',
    'class' => 'tripal-organism-data-table tripal-data-table',
  ), 
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(), 
  'empty' => '', 
); 

$table_assembly = array(
  'header' => $headers, 
  'rows' => $rowsAnalysis, 
  'attributes' => array(
    'id' => 'legume_organism_analysis',
    'class' => 'legume_organism_analysis_tab',
  ), 
  'sticky' => FALSE,
  'caption' => '<b>Assembly Data</b>',
  'colgroups' => array(), 
  'empty' => '', 
); 

/*eksc- use top table for project information
$table_BioProject = array(
  'header' => $headers, 
  'rows' => $ProjectData, 
  'attributes' => array(
    'id' => 'legume_organism_analysis',
    'class' => 'legume_organism_analysis_tab',
  ), 
  'sticky' => FALSE,
  'caption' => 'Project Data',
  'colgroups' => array(), 
  'empty' => '', 
); 
*/
$table_SampleData = array(
  'header' => $headers, 
  'rows' => $SampleData, 
  'attributes' => array(
    'id' => 'legume_organism_analysis',
    'class' => 'legume_organism_analysis_tab',
  ), 
  'sticky' => FALSE,
  'caption' => '<b>Sample Data</b>',
  'colgroups' => array(), 
  'empty' => '', 
); 

// once we have our table array structure defined, we call Drupal's theme_table()
// function to generate the table.
print theme_table($table);
print theme_table($table_SampleData );
//print theme_table($table_BioProject );
print theme_table($table_assembly );
 ?>

