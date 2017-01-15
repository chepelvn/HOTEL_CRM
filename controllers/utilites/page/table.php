<?php
namespace ul\page;

class table{
    public function setColumn($name = null){
    	$this->__item[$name] = array();
    	$this->current = &$this->__item[$name];
    	$this->current['name'] = $name;
    	$this->current['visible'] = true;
    	return $this;
    }

    public function setTitle($title = null){
    	$this->current['title'] = $title;
    	return $this;
    }

    public function setProp($name, $value = null){
    	if(is_array($name)){
    	 	foreach($name as $key => $val){
    	 		$this->current['props'][$key] = $val;
    	    }
    	} else {
    	   $this->current['props'][$name] = $value;
    	}
    	return $this;
    }

    public function getItem($name = null){
        if(isset($name)){
        	if($this->__item[$name]) $this->current = &$this->__item[$name];
        } else {
        	 $keys = array_keys($this->__item);
           	 $this->current = &$this->__item[$keys[0]];
        }

        return $this;
    }

    public function getProp($name = null){
         return $this->current['props'][$name];
    }

    public function show($of = null){
        $of_exp = (is_array($of) ? $of : explode(',', str_replace(' ', '', $of)));
        foreach($this->__item as $key => &$val){
        	if(!in_array($key, $of_exp) && !$val['not-show']){
        		unset($val['visible']);
            } else {
            	$val['visible'] = true;
            }
        }
        return $this;
    }

    public function notShow($of = null){
        if(!$of){
        	 $this->current['not-show'] = true;
        } else {
	        $of_exp = (is_array($of) ? $of : explode(',', str_replace(' ', '', $of)));
	        foreach($this->__item as $key => &$val){
	        	if(in_array($key, $of_exp)){
	        		$val['not-show'] = true;
	            }
	        }
	     }
        return $this;
    }

    public function getItemArray(){
        if(isset($name))
        	return $this->__item[$name];

        foreach($this->__item as $a){
           	return current($this->__item);
        }
    }

    public function setQueryStr($asc = null, $desc = null){
    	if($asc) $this->setProp('asc', $asc);
    	if($desc) $this->setProp('desc', $desc);
    	$this->setProp('is-ordered', true);
        return $this;
    }

    public function getColumnsArray(){
       return $this->__item;
    }
}