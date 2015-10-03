<?php
	echo "Start\r\n";


	function oa($a)
	{
		echo '<pre>';
		var_dump($a);
		echo '</pre>';
	}
if ((isset($_GET['pass']) && md5($_GET['pass']) == md5('DbJ4BO1WTJp1kgFSk7dU')))
{
//if ($_SERVER['HTTP_HOST'] == NULL){
	include_once(dirname(__FILE__).'/application/includes.inc.php');
	include_once(ENGINE_PATH.'class/classPage.php');
	include_once(ENGINE_PATH.'class/classCase.php');
	
		error_reporting(E_ALL);
	ini_set('display_errors', 1);
		
	VBox::set('ConstData', new ConstData());	

	//Count case on each page
	$count_case_on_page = IniParser::getSettring('case', 'step') ? IniParser::getSettring('case', 'step') : 3;
	
	if ((int)$count_case_on_page > 0){

		  //	Очистим таблицу case2page
		  $q = "TRUNCATE TABLE case2page";
		  
		  DB::executeAlter($q);
	  
		  // Выбираем все уникальные значения языков страниц
		  $q="select distinct pg_lang  from pages where pg_hidden = ?";
		  
		  DB::executeQuery($q, 'langs_data', array(0));
		  
		  $langs_data = DB::fetchResults('langs_data');
	  
		  //Запрос для выборки активных страниц для данного языка
		  $q_page="select pg_id from pages where pg_hidden = ? and pg_lang = ? and pg_id <>?";
		  //Запрос для выборки активных case для данного языка
		  $q_case="select cs_id from ".VBox::get('ConstData')->getConst('langsDb').".case_study where cs_hidden = ? and cs_lang_id = ?";	
		  
		  $q_res="";
		  
		  // Делаем цикл по языкам
		  foreach ($langs_data as $lang){
			  // Выбираем все активные страницы для данного языка
			  DB::executeQuery($q_page, 'pages_data_'.$lang['pg_lang'], array(0, $lang['pg_lang'], VBox::get('ConstData')->getConst('loginPage') ));
		  
			  $pages_data = DB::fetchResults('pages_data_'.$lang['pg_lang']);
	  
			  // Выбираем все активные case для данного языка
			  DB::executeQuery($q_case, 'case_data_'.$lang['pg_lang'], array(0, $lang['pg_lang']));
		  
			  $cases_data = DB::fetchResults('case_data_'.$lang['pg_lang']);
			  
			  // Определим колво елементов case
			  $count_case = sizeof($cases_data);
			  $cases = array();
			  foreach($cases_data as $case){
				  array_push($cases, $case['cs_id']);	
			  }
			  
			  //Перемешиваем массив случайным образом
			  shuffle($cases);
	  
			  //Счетчик цикла
			  $j=0;
			  //Делаем цикл по кол-ву case на каждой странице
			  for ($i=0; $i<$count_case_on_page; $i++ ){
				  //Делаем цикл по всем страницам
				  foreach($pages_data as $page){
					  if ($j >= $count_case){
						   $j = 0;
					  }
					  
					  $q_res.=sprintf("(%d,%d),",$page['pg_id'], $cases[$j]);
					  
					  $j++;				
				  }//foreach($pages_data as $page)
			  }
		  }//foreach ($langs_data as $lang)
	  
		  $q_res = "INSERT INTO case2page (cp_pg_id, cp_cs_id) values ".substr($q_res,0,-1);	
	  
	  //	oa($q_res);
		  
		  DB::executeAlter($q_res);
	}
    include_once(LOCAL_PATH.'application/final.inc.php');	
	echo "Ok\r\n";
}else{
	echo "Error\r\n";	
}

?>