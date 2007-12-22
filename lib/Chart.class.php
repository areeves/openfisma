<?php
/**
 * 
 * This file is a helper file used to generate charts.
 * 
 */

require_once('../lib/Config.class.php');
require_once('Image/Graph.php');


class Chart {


	/**
	 * 
	 * VARIABLES
	 * 
	 */

	private $img_dir;
	
	private $font_face;
	private $font_size;
	
	private $title_font_size;
	private $title_area_size;
	
	private $bg_color;
	private $color_array;

	private $graph_width;
	private $graph_height;

	private $plot_line_color;
	
	private $marker_fill_color;
	private $marker_border_color;

	private $pie_explode;

	/**
	 * 
	 * CLASS METHODS
	 * 
	 */


	public function __construct($img_dir = './') {
		
		
		$this->setImgDir($img_dir);
		$this->setBGColor('white');
		$this->setColorArray(array('red', 'orange', 'yellow', 'green', 'blue', 'purple', 'brown'));
	
		$this->setFontFace('Verdana');
		$this->setFontSize(7);
		
		$this->setTitleFontSize(10);
		$this->setTitleAreaSize(10); 
		
		$this->setGraphWidth(300);
		$this->setGraphHeight(225);
		
		$this->setPlotLineColor('gray');
		
		$this->setMarkerFillColor('white');
		$this->setMarkerBorderColor('black');
		
		$this->setPieExplode(5);
		
		
	}
	
	
	public function __destruct() {}


	public function __ToString() {}
		
		
	/**
	 * 
	 * SETTER METHODS
	 * 
	 */

	public function setColorArray($color_array = array('red', 'orange', 'yellow', 'green', 'blue', 'purple', 'brown')) { $this->color_array = $color_array; }
	
	public function setImgDir($img_dir = './')                           { $this->img_dir             = $img_dir;             }
	public function setBGColor($bg_color = 'white')                      { $this->bg_color            = $bg_color;            }
	public function setFontFace($font_face = 'Verdana')                  { $this->font_face           = $font_face;           }
	public function setFontSize($font_size = 7)                          { $this->font_size           = $font_size;           }
	public function setTitleFontSize($title_font_size = 10)              { $this->title_font_size     = $title_font_size;     }
	public function setTitleAreaSize($title_area_size = 10)              { $this->title_area_size     = $title_area_size;     }
	public function setGraphWidth($graph_width  = 300)                   { $this->graph_width         = $graph_width;         }
	public function setGraphHeight($graph_height = 225)                  { $this->graph_height        = $graph_height;        }
	public function setPlotLineColor($plot_line_color = 'gray')          { $this->plot_line_color     = $plot_line_color;     }
	public function setMarkerFillColor($marker_fill_color = 'white')     { $this->marker_fill_color   = $marker_fill_color;   }
	public function setMarkerBorderColor($marker_border_color = 'black') { $this->marker_border_color = $marker_border_color; }		
	public function setPieExplode($pie_explode = 5)                      { $this->pie_explode         = $pie_explode;         }


	/**
	 * 
	 * INSTANCE METHODS
	 * 
	 */


	public function generatePieChart($file_name = 'chart_pie.png', $title = 'Pie Chart', $data) {
	
		// create the graph
		$graph =& Image_Graph::factory('Image_Graph', array($this->graph_width, $this->graph_height));

		// create font settings
		$font =& $graph->addNew('Image_Graph_Font', $this->font_face);
		$font->setSize($this->font_size);
		
		// add the font settings
		$graph->setFont($font);

		// create a title
		$title_area = Image_Graph::factory('Image_Graph_Title', array($title, $this->title_font_size));
		
		// create a plot area
		$plot_area = Image_Graph::factory('Image_Graph_Plotarea');
		$plot_area->hideAxis();
				
		// add the title and plot area
		$graph->add( Image_Graph::vertical($title_area, $plot_area, $this->title_area_size ));

		// add in the legend
		$legend =& $plot_area->addNew('Image_Graph_Legend');
		$legend->setAlignment(IMAGE_GRAPH_ALIGN_TOP_LEFT);
		$legend->setLineColor(NULL);

		// create the dataset and color scheme
		$plot_data  =& Image_Graph::factory('Image_Graph_Dataset_Trivial');
		$fill_array =& Image_Graph::factory('Image_Graph_Fill_Array');

		// create some color information
		$color_array = $this->color_array;
		
		// add in each data point and give it a color
		foreach ($data as $key => $val) { 
			
			$plot_data->addPoint($key, $val);		
			$fill_array->addNew('Image_Graph_Fill_Gradient', array(IMAGE_GRAPH_GRAD_RADIAL, $this->bg_color, array_shift($color_array)));
		
		}
		
		// create a pie plot with the data set
		$plot =& $plot_area->addNew('Image_Graph_Plot_Pie', $plot_data);
		$plot->setFillStyle($fill_array);		
		$plot->setLineColor($this->plot_line_color);
		$plot->explode($this->pie_explode);

		// create a Y data value marker
		$marker =& $plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_PCT_Y_TOTAL);
		$marker->setFillColor($this->marker_fill_color);
		$marker->setBorderColor($this->marker_border_color);
		$marker->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.1f%%'));
		$marker->setFontSize($this->font_size);

		// create a pin-point marker type
		$pointingMarker =& $plot->addNew('Image_Graph_Marker_Pointing_Angular', array(-25, &$marker));
		$pointingMarker->setLineColor(FALSE);
		
		// add the marker
		$plot->setMarker($pointingMarker);
       
		// output the Graph
		$graph->done(array('filename'=>$this->img_dir.$file_name));
	
	}


	public function generateBarChart($file_name = 'chart_pie.png', $title = 'Bar Chart', $data) {
	
	
			// create the graph
		$graph =& Image_Graph::factory('Image_Graph', array($this->graph_width, $this->graph_height));

		// create font settings
		$font =& $graph->addNew('Image_Graph_Font', $this->font_face);
		$font->setSize($this->font_size);
		
		// add the font settings
		$graph->setFont($font);

		// create a title
		$title_area = Image_Graph::factory('Image_Graph_Title', array($title, $this->title_font_size));
		
		// create a plot area
		$plot_area = Image_Graph::factory('Image_Graph_Plotarea');
		//$plot_area->hideAxis();
				
		// add the title and plot area
		$graph->add( Image_Graph::vertical($title_area, $plot_area, $this->title_area_size ));

		// create the dataset and color scheme
		$plot_data  =& Image_Graph::factory('Image_Graph_Dataset_Trivial');
		$fill_array =& Image_Graph::factory('Image_Graph_Fill_Array');
		
		$color_array = $this->color_array;
		
		// add in each data point and give it a color
		foreach ($data as $key => $val) { 
			
			$plot_data->addPoint($key, $val);		
			$fill_array->addNew('Image_Graph_Fill_Gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, $this->bg_color, array_shift($color_array)));
		
		}
		
		// create a pie plot with the data set
		$plot =& $plot_area->addNew('Image_Graph_Plot_Bar', $plot_data);
		$plot->setFillStyle($fill_array);		
		$plot->setLineColor($this->plot_line_color);

		// create a Y data value marker
		$marker =& $plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);
		$marker->setFillColor($this->marker_fill_color);
		$marker->setBorderColor($this->marker_border_color);
		$marker->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%d'));
		$marker->setFontSize($this->font_size);

		// create a pin-point marker type
		$pointingMarker =& $plot->addNew('Image_Graph_Marker_Pointing', array(0, $this->font_size, &$marker));
		$pointingMarker->setLineColor(FALSE);
		
		// add the marker
		$plot->setMarker($pointingMarker);
       
		// output the Graph
		$graph->done(array('filename'=>$this->img_dir.$file_name));
	
	}


		
} // Chart


$_CHART = new Chart($_CONFIG->APP_IMG());



?>