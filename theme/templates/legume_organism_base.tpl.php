<?php

$organism  = $variables['node']->organism;
//$organism = chado_expand_var($organism,'table','genome_metadata');

$organism = chado_expand_var($organism  , 'table', 'stock');
//$organism = chado_expand_var($organism,'field','organism.comment');
drupal_set_message("I start printing  all  444444 ");
//drupal_set_message('resws: <pre>' . print_r($organism->stock, true) . '</pre>');
$stocks = $organism->stock;
foreach ($stocks as $stock) {
  $stock = chado_expand_var($stock, 'table', 'genome_metadata');
  //$stock = chado_expand_var($stock, 'table', 'analysisprop');
  $stock = chado_expand_var($stock, 'table', 'projectprop');
   $stock = chado_expand_var($stock, 'table', 'project_dbxref');
  // $stock = chado_expand_var($stock, 'table', 'stockcollection');
   $stock = chado_expand_var($stock, 'table', 'nd_experimentprop');
   $stock = chado_expand_var($stock, 'table', 'nd_experiment_dbxref');
   $stock = chado_expand_var($stock, 'table', 'stockprop');
   $stock = chado_expand_var($stock, 'table', 'stock_dbxref');
    $stock = chado_expand_var($stock, 'table', 'analysisprop');
  if($stock->genome_metadata) 
      {
         drupal_set_message("I am here inside");
         $genomemetadata=$stock->genome_metadata;
         $x=$stock;
      }
}
drupal_set_message('results of metadata: <pre>' . print_r($genomemetadata, true) . '</pre>');
drupal_set_message($genomemetadata->project_id->name);
drupal_set_message('results 2: <pre>' . print_r($genomemetadata->project_id->projectprop[9]->value, true) . '</pre>');
// drupal_set_message('results: <pre>' . print_r($stocks[0], true) . '</pre>');
//drupal_set_message("I start printing");
 ?>

<div class="tripal_organism-data-block-desc tripal-data-block-desc"></div><?php

// generate the image tag
$image = '';
$image_url = tripal_get_organism_image_url($organism); 
if ($image_url) {
  $image = "<img class=\"tripal-organism-img\" src=\"$image_url\">";
}

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
//Project id row
$rows[] = array(
  array(
    'data' => 'Project Id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->project_id->project_id . '</i>'
);
 $proj= db_query(
    'SELECT * FROM public.chado_project WHERE project_id LIKE :project_id',
    array(':project_id' => 104)
  )->fetchAll();
// Project Name row
$rows[] = array(
  array(
    'data' => 'Project Name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<a href="/node/'.$genomemetadata->project_id->nid.'">' . $genomemetadata->project_id->name . '</a>'
);

//analysis
$rows[] = array(
  array(
    'data' => 'Analysis Id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->analysis_id->analysis_id . '</i>'
);


$rows[] = array(
  array(
    'data' => 'Executed Time', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->analysis_id->timeexecuted . '</i>'
);
//experiment id
$rows[] = array(
  array(
    'data' => 'Experiment Id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->nd_experiment_id . '</i>'
);

//location

$rows[] = array(
  array(
    'data' => 'Location Id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->nd_geolocation_id->nd_geolocation_id . '</i>'
);


$rows[] = array(
  array(
    'data' => 'Location Description', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->nd_geolocation_id->description . '</i>'
);


//type_id

$rows[] = array(
  array(
    'data' => 'type name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->name . '</i>'
);


$rows[] = array(
  array(
    'data' => 'type definition', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->definition . '</i>'
);

//dbxref


$rows[] = array(
  array(
    'data' => 'dbxref_id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->dbxref_id->dbxref_id . '</i>'
);


$rows[] = array(
  array(
    'data' => 'db id', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->dbxref_id->db_id->db_id . '</i>'
);

$rows[] = array(
  array(
    'data' => 'db name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->dbxref_id->db_id->name . '</i>'
);

$rows[] = array(
  array(
    'data' => 'Accession', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->nd_experiment_id->type_id->dbxref_id->accession . '</i>'
);
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

$rows[] = array(
  array(
    'data' => 'stock name', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<a href="/node/'.$results[0]->nid.'">' . $genomemetadata->stock_id->name . '</a>'
);

$rows[] = array(
  array(
    'data' => 'Project Data', 
    'header' => TRUE,
    'width' => '30%',
  )
 
);

$rows[] = array(
  array(
    'data' => 'BioProject', 
    'header' => TRUE,
    'width' => '20%',
  )
 
);

$rows[] = array(
  array(
    'data' => $genomemetadata->project_id->projectprop[9]->type_id->name , 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $genomemetadata->project_id->projectprop[9]->value . '</i>'
);
// drupal_set_message($genomemetadata->project_id->projectprop.length);
// for($i=0;$i<$genomemetadata->project_id->projectprop.length;$i++)
// {
// drupal_set_message($i);
// }

foreach($genomemetadata->project_id->projectprop as $value) {
$rows[] = array(
  array(
    'data' => $value->type_id->name , 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $value->value . '</i>'
);
}
$rows[] = array(
 array(
    'data' => "Sample Data" , 
    'header' => TRUE,
    'width' => '20%',
  ) 
);

foreach($genomemetadata->nd_experiment_id->nd_experimentprop as $value) {
$rows[] = array(
  array(
    'data' => $value->type_id->name , 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $value->value . '</i>'
);
}


$rows[] = array(
 array(
    'data' => "Assembly" , 
    'header' => TRUE,
    'width' => '20%',
  ) 
);

foreach($genomemetadata->analysis_id->analysisprop as $value) {
$rows[] = array(
  array(
    'data' => $value->type_id->name , 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $value->value . '</i>'
);
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

// allow site admins to see the organism ID
if (user_access('view ids')) {
  // Organism ID
  $rows[] = array(
    array(
      'data'   => 'Organism ID',
      'header' => TRUE,
      'class'  => 'tripal-site-admin-only-table-row',
    ),
    array(
     'data'  => $organism->organism_id,
     'class' => 'tripal-site-admin-only-table-row',
    ),
  );
}

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

// once we have our table array structure defined, we call Drupal's theme_table()
// function to generate the table.
print theme_table($table); ?>
<div style="text-align: justify"><?php print $image . $organism->comment?></div>  