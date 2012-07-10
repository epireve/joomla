<?php

/** @file
 * This file is part of Google Chart PHP library.
 *
 * Copyright (c) 2010 Rémi Lanvin <remi@cloudconnected.fr>
 *
 * Licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE file.
 */
 
/**
 * A data serie.
 *
 * This class implement every feature that is directly related to a data serie
 * or its representation in the chart.
 *
 * Some method won't work for all charts, but won't produce an error.
 */
class GoogleChartData
{
	/**
	 * An array of the values of the data serie.
	 */
	protected $values = null;
	/**
	 *  The name of the data serie to be displayed as legend.
	 */
	protected $legend = null;
	/**
	 * The label of the values of the data serie. Pie Chart only.
	 */
	protected $labels = null;

	/**
	 * Indicate if the color has been overriden.
	 * This variable is used to minimize the request. If no custom color has
	 * been providen, then the @c cho parameter is not triggered.
	 */
	protected $chco = false;
	/**
	 * Color of the data serie (string or array)
	 * Default color by Google Chart API is ffcc33
	 */
	protected $color = 'ffcc33';

	/**
	 * Indicate if @c chls parameter is needed
	 */
	protected $chls = false;

	/**
	 * Thickness of the line. Line Chart only. (@c chls)
	 */
	protected $thickness = 2;
	
	/**
	 * Length of the dash. Line Chart only. (@c chls)
	 */
	protected $dash_length = null;
	
	/**
	 * Length of the spaces between dashes. Line Chart only. (@c chls)
	 */
	protected $space_length = null;

	/**
	 *  Line fill values (to fill area below a line). (@c chm)
	 */
	protected $fill = null;
	/**
	 *  bool Wether to calculate scale automatically or not.
	 */
	protected $autoscale = true;
	/**
	 *  array The scale, as specified by the user with setScale
	 */
	protected $scale = null;
	
	/**
	 *  int Holds the index of the data serie in the chart. Null if not added.
	 */
	protected $index = null;

	/**
	 * Create a new data serie.
	 */
	public function __construct($values)
	{
		if ( $values !== null && ! is_array($values) )
			throw new InvalidArgumentException('Invalid values (must be an array or null)');

		$this->values = $values;
	}

	/**
	 * Returns the values of this dataserie
	 * @return array (or null)
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * @since 0.5
	 */
	public function hasValues()
	{
		return $this->values !== null && ! empty($this->values);
	}
	
	/**
	 * @since 0.5
	 */
	public function computeChd($encoding = GoogleChart::TEXT, $scale = null)
	{
		// if scale is null, it means that there is not "global" scale for the chart
		// hence we need to determine the scale for this data only
		if ( $scale === null ) {
			$scale = $this->getScale();
		}

		switch ( $encoding ) {
			case GoogleChart::TEXT :
				return self::encodeText($this->values, $scale['min'], $scale['max']);
			case GoogleChart::SIMPLE_ENCODING :
				return self::encodeSimple($this->values, $scale['min'], $scale['max']);
			case GoogleChart::EXTENDED_ENCODING :
				return self::encodeExtended($this->values, $scale['min'], $scale['max']);
			default:
				throw new InvalidArgumentException('Invalid encoding format');
		}
	}
	
	
/**
 * @name Pie Chart Labels @c chl
 */
//@{
	/**
	 * @since 0.5
	 */
	public function setLabelsAuto()
	{
		return $this->setLabels(array_keys($this->values));
	}

	/**
	 * @since 0.5
	 */
	public function setLabels($labels)
	{
		$n = sizeof($labels);
		$v = sizeof($this->values);
		if ( $n > $v ) {
			throw new InvalidArgumentException('Invalid labels, to many labels');
		}
		elseif ( $n < $v ) {
			$labels += array_fill(0, $v-$n, '');
		}

		$this->labels = $labels;
		return $this;
	}
	
	/**
	 * Return labels set by setLabels()
	 * @return array();
	 */
	public function getLabels()
	{
		return $this->labels;
	}

	/**
	 * Compute @c chl parameter.
	 *
	 * Only for Pie Chart.
	 *
	 * If the chart has no label, this function returns a string containing
	 * an empty label for each value (example "|" for 2 values, "||" for 3, etc.).
	 * This way, labels are always in sync with the values. The case happens
	 * with a concentric chart, if the inner chart (first data serie) doesn't
	 * have label, but the outer chart (second data serie) has.
	 *
	 * @internal
	 * @since 0.5
	 */
	public function computeChl()
	{
		if ( ! $this->values )
			return '';

		if ( $this->labels === null ) {
			return str_repeat('|',sizeof($this->values)-1);
		}
		return implode('|',$this->labels);
	}
//@}

	/**
	 * Set the index of the data serie in the chart.
	 *
	 * @internal
	 * @note Used by GoogleChart when calling GoogleChart::addData()
	 * @param $index (int)
	 * @return $this
	 */
	public function setIndex($index)
	{
		if ( ! is_int($index) )
			throw new InvalidArgumentException('Invalid index (must be an integer)');

		$this->index = (int) $index;
		return $this;
	}

	/**
	 * Return the index of the data serie in the chart (null if not in a chart).
	 *
	 * @return int or null
	 */
	public function getIndex()
	{
		return $this->index;
	}

	/**
	 * Returns true if the data serie has an index, false otherwise.
	 *
	 * @return bool
	 */
	public function hasIndex()
	{
		return $this->index !== null;
	}

	/**
	 * Enable/disabled autoscaling.
	 * @param $autoscale (bool)
	 * @return $this
	 */
	public function setAutoscale($autoscale)
	{
		$this->autoscale = $autoscale;
		return $this;
	}

	/**
	 * Set the scale of this data serie.
	 * When using this function, be sure your turned off global autoscaling.
	 * @see http://code.google.com/p/googlechartphplib/wiki/Autoscaling
	 * @param $min (int)
	 * @param $max (int)
	 */
	public function setScale($min, $max)
	{
		$this->setAutoscale(false);
		$this->scale = array(
			'min' => $min,
			'max' => $max
		);
		return $this;
	}

	/**
	 * @since 0.5
	 */
	public function getScale()
	{
		if ( $this->autoscale == true ) {
			if ( ! empty($this->values) ) {
				$n = min($this->values);
				if ( $n > 0 )
					$n = 0;
				return array('min' => $n, 'max' => max($this->values));
			}
		}

		if ( $this->scale === null ) {
			return array('min' => 0, 'max' => 100);
		}

		return $this->scale;
	}
	
	/**
	 * @since 0.5
	 */
	public function computeChds()
	{
		$scale = $this->getScale();
		return $scale['min'].','.$scale['max'];
	}

	/**
	 * @since 0.5
	 */
	public function hasCustomScale()
	{
		return $this->scale !== null || $this->autoscale;
	}

	/**
	 * Chart Legend (chdl)
	 *
	 * @param $legend (string)
	 */
	public function setLegend($legend)
	{
		$this->legend = $legend;
		return $this;
	}

	/**
	 * Return the legend.
	 * @return string
	 */
	public function getLegend()
	{
		return $this->legend;
	}

	/**
	 * Return true if a legend has been set
	 * @return bool
	 */
	public function hasCustomLegend()
	{
		return $this->legend !== null;
	}

/**
 * @name Data Serie Color (@c chco).
 */
//@{
	/**
	 * Set the serie color.
	 * Color can be an array for bar charts and pie charts.
	 *
	 * @param $color (mixed) a RRGGBB string, or an array for Bar Chart and Pie Chart
	 * @see http://code.google.com/apis/chart/docs/chart_params.html#gcharts_series_color
	 */
	public function setColor($color)
	{
		$this->chco = true;
		$this->color = $color;
		return $this;
	}

	/**
	 * Return the serie colors.
	 * @return color
	 */
	public function getColor()
	{
		return $this->color;
	}
	
	/**
	 * Compute the @c cho parameter.
	 * @internal
	 * @return string
	 */
	public function computeChco()
	{
		if ( is_array($this->color) )
			return implode('|',$this->color);

		return $this->color;
	}

	/**
	 * Return true if parameter @chco is needed
	 * @return true
	 */
	public function hasChco()
	{
		return $this->chco;
	}
//@}

	/**
	 * Line fill (chm)
	 *
	 * @see http://code.google.com/apis/chart/docs/chart_params.html#gcharts_line_fills
	 */
	public function setFill($color)
	{
		$this->fill = array(
			'color' => $color
		);
	}

	/**
	 * @todo Move to compute*
	 */
	public function getFill($compute = true)
	{
		if ( ! $compute )
			return $this->fill;
		
		if ( $this->fill === null )
			return null;

		$fill = 'B,'.$this->fill['color'].',%d,0,0';

		return $fill;
	}

/**
 * @name Line styles (chls).
 */
// @{

	/**
	 * Set the thickness of the line (Line Chart only).
	 *
	 * @see http://code.google.com/apis/chart/docs/chart_params.html#gcharts_line_styles
	 * @since 0.5
	 */
	public function setThickness($thickness)
	{
		$this->chls = true;

		$this->thickness = $thickness;
		return $this;
	}
	
	/**
	 * @since 0.5
	 */
	public function getThickness()
	{
		return $this->thickness;
	}

	/**
	 * @since 0.5
	 */
	public function setDash($dash_length, $space_length = null)
	{
		$this->chls = true;

		$this->dash_length = $dash_length;
		$this->space_length = $space_length;
		return $this;
	}
	
	/**
	 * @internal
	 * @since 0.5
	 */
	public function computeChls()
	{
		$str = $this->thickness;
		if ( $this->dash_length !== null ) {
			$str .= ','.$this->dash_length;
			if  ( $this->space_length !== null ) {
				$str .= ','.$this->space_length;
			}
		}
		return $str;
	}
	
	/**
	 * @internal
	 * @since 0.5
	 */
	public function hasChls()
	{
		return $this->chls;
	}
//@}

	/**
	 * @internal
	 * @since 0.5
	 */
	static public function encodeText(array $values)
	{
		foreach ( $values as & $v ) {
			if ( $v === null ) {
				$v = '_';
			}
		}
		return implode(',',$values);
	}

	/**
	 * @internal
	 * @since 0.5
	 */
	static public function encodeSimple(array $values, $min = null, $max = null)
	{
		if ( $min === null ) {
			$min = min($values);
			// by default, we only want a min if there is negative values
			if ( $min > 0 ) {
				$min = 0;
			}
		}
		if ( $max === null ) {
			$max = max($values);
		}
		$max = $max + abs($min);

		$map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		
		foreach ( $values as $v ) {
			if ( $v === null ) {
				$str .= '_';
				continue;
			}
			
			$n = round(61 * (($v - $min) / $max));
			if ( $n > 61 ) {
				$str .= '9';
			}
			elseif ( $n < 0 ) {
				$str .= '_';
			}
			else {
				$str .= $map[$n];
			}
		}
		return $str;
	}

	/**
	 * @internal
	 * @since 0.5
	 */
	static public function encodeExtended(array $values, $min = null, $max = null)
	{
		if ( $min === null ) {
			$min = min($values);
			// by default, we only want a min if there is negative values
			if ( $min > 0 ) {
				$min = 0;
			}
		}
		if ( $max === null ) {
			$max = max($values);
		}
		$max = $max + abs($min);

		$map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
		$str = '';
		
		foreach ( $values as $v ) {
			if ( $v === null ) {
				$str .= '__';
				continue;
			}

			$n = floor(64 * 64 * (($v - $min) / $max));
			if ( $n > (64*64 - 1) ) {
				$str .= '..';
			}
			elseif ( $n < 0 ) {
				$str .= '__';
			}
			else {
				$q = floor($n / 64);
				$r = $n - 64 * $q;
				$str .= $map[$q].$map[$r];
			}
		}
		return $str;
	}
}
