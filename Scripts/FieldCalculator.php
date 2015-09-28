<?php

class Field_Calculate {
    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';

    const PARENTHESIS_DEPTH = 10;

    public function calculate($input){
    	
       // if(strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null){
            //  Remove white spaces and invalid math chars
            $input = str_replace(',', '.', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)\D\d]', '', $input);

            //  Calculate each of the parenthesis from the top
            $i = 0;
            while(strpos($input, '(') || strpos($input, ')')){
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);
print(")".$i."-".$input);
                $i++;
                if($i > self::PARENTHESIS_DEPTH){
                    break;
                }
            }

            //  Calculate the result
            
          //  if(preg_match(self::PATTERN, $input, $match)){
                return $this->compute($match[0]);
          //  }

            return 0;
       // }

      //  return $input;
    }

    private function compute($input){
        $compute = create_function('', 'return '.$input.';');

        return 0 + $compute();
    }

    private function isRoll($input)
    {
    	$cad = strtoupper($input);
    	print("->1->".$cad);
    	$tok = explode("D", $cad);
    	if(sizeof($tok)!=2) return false;
    	elseif(!is_numeric($tok[0])) return false;
    	elseif(!is_numeric($tok[1])) return false;
    	else return true;
    }
    
    private function Roll($input)
    {
    	
    	$cad = strtoupper($input);
    	$tok = explode("D", $cad);
    	$roll = 0;
    	for($i=1;$i<$tok[0];$i++)
    	{
    		$roll+=rand(1,$tok[1]);
    	}
    	return $roll;
    }
    private function callback($input){
    	print("->2->".$cad);
        if(is_numeric($input[1])){
            return $input[1];
        }
        elseif(isRoll($input[1]))
        {
        	return Roll($input[1]);
        }
        elseif(preg_match(self::PATTERN, $input[1], $match)){
        	print("-3".$input[1]);
            return $this->compute($match[0]);
        }

        return 0;
    }
}
?>