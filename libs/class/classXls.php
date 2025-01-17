<?php
class xls{
	private $column;
	private $row;
	private $cell;
	private $xlcontent;
	private $colmax;
	private $rowmax;
	private $msxml;
	private $cellstyle;
	
	public function __construct()
	{
		$this->colmax=0;
		$this->rowmax=0;
$this->msxml = "<html xmlns:o='urn:schemas-microsoft-com:office:office'
xmlns:w='urn:schemas-microsoft-com:office:excel'
xmlns='http://www.w3.org/TR/REC-html40'>

<head>
<meta http-equiv=Content-Type content='text/html; charset=utf-8'>
<meta name=ProgId content=Excel.Document>
<meta name=Generator content='Microsoft Excel 11'>
<meta name=Originator content='Microsoft Excel 11'>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>Garbagecat Software</o:Author>
  <o:Pages>1</o:Pages>
 </o:DocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:ExcelDocument>
  <w:View>Print</w:View>
  <w:Zoom>80</w:Zoom>
  <w:SpellingState>Clean</w:SpellingState>
  <w:GrammarState>Clean</w:GrammarState>
  <w:PunctuationKerning/>
  <w:ValidateAgainstSchemas/>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:Compatibility>
   <w:BreakWrappedTables/>
   <w:SnapToGridInCell/>
   <w:WrapTextWithPunct/>
   <w:UseAsianBreakRules/>
   <w:DontGrowAutofit/>
  </w:Compatibility>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:ExcelDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState='false' LatentStyleCount='156'>
 </w:LatentStyles>
</xml><![endif]-->
<style>
<!--
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:'';
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:'Times New Roman';
	mso-fareast-font-family:'Times New Roman';}
@page Section1
	{size:595.3pt 841.9pt;
	margin:72.0pt 90.0pt 72.0pt 90.0pt;
	mso-header-margin:35.4pt;
	mso-footer-margin:35.4pt;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:'Table Normal';
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:'';
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:'Times New Roman';
	mso-ansi-language:#0400;
	mso-fareast-language:#0400;
	mso-bidi-language:#0400;}
</style>
<![endif]-->
</head>

<body lang=EN-GB>
";
	}
	
	public function add_cell($strcell, $content)
	{
		if(strpos($strcell,":") != false)
		{
			list($this->column,$this->row) = explode(":",$strcell);
			$this->cell[$this->column][$this->row]=$content;
			if($this->column > $this->colmax)
				$this->colmax = $this->column;
			if($this->row > $this->rowmax)
				$this->rowmax = $this->row;
				
		}
	}
	
	public function cell_style($strcell, $style, $attr=null)
	{
		if(strpos($strcell,":") != false)
		{
			list($this->column,$this->row) = explode(":",$strcell);
			$this->cellstyle[$this->column][$this->row]=$style;
            
            if (isset($attr))
                $this->cellstyle[$this->column][$this->row]['attr']=$attr;
            
			if($this->column > $this->colmax)
				$this->colmax = $this->column;
			if($this->row > $this->rowmax)
				$this->rowmax = $this->row;
				
		}
	}
	
	private function prepare_content()
	{
		$this->xlcontent="<table border='1'>";
		for($r=1;$r<=$this->rowmax;$r++)
		{
			$this->xlcontent .= "<tr>";
			for($c=1;$c<=$this->colmax;$c++)
			{
				if($this->cell[$c][$r]!="" || $this->cell[$c][$r]!=NULL || $this->cell[$c][$r]['attr']!="" || $this->cell[$c][$r]['attr']!=NULL)
				{
					if($this->cellstyle[$c][$r]!="" || $this->cellstyle[$c][$r]!=NULL || $this->cell[$c][$r]['attr']!="" || $this->cell[$c][$r]['attr']!=NULL)
						$this->xlcontent .= "<td style=\"".$this->cellstyle[$c][$r]."\" ".$this->cellstyle[$c][$r]['attr'].">".$this->cell[$c][$r]."</td>";
					else
						$this->xlcontent .= "<td>".$this->cell[$c][$r]."</td>";
 					
				}
				else
				{
					if($this->cellstyle[$c][$r]!="" || $this->cellstyle[$c][$r]!=NULL || $this->cell[$c][$r]['attr']!="" || $this->cell[$c][$r]['attr']!=NULL)
						$this->xlcontent .= "<td style=\"".$this->cellstyle[$c][$r]."\" ".$this->cellstyle[$c][$r]['attr'].">&nbsp;</td>";
					else
						$this->xlcontent .= "<td>&nbsp;</td>";
				}
			}
			$this->xlcontent .= "</tr>";
		}
		$this->xlcontent .= "</table></body></html>";
		$returnstring = $this->msxml.$this->xlcontent;
		return $returnstring;
	}
	
	public function execute($filename)
	{
		if($filename!="" || $filename!=NULL)
		{
  
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
           	header("Content-Disposition: attachment; filename=\"".$filename."\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);


		}
		else
		{   
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
           	header("Content-Disposition: attachment; filename=\"download.xls");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
 		}
			echo $this->prepare_content();
	}
	
}