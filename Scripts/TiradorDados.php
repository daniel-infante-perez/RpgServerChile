<?php

/** Clase RespuestaDados
 * Created by PhpStorm.
 * User: Daniel
 * Date: 28-09-2015
 * Time: 12:51
 */

class RespuestaDados
{
    public  $expresion;         // primitiva
    public  $numeroVeces;       // Nv
    public  $repetir;           // Rp
    public  $frase;             // Frase de dado que se tiró
    public  $dados;             // Resultados de cada dado, array de enteros, multidimensional
    public  $suma;              // array de sumas de cada tirada de dados
    public  $tiradaOk;          // true|false
    public  $mensajeError;      // En caso de que haya habido un error


    public function FormatoJsonWebhook()
    {

        $respuesta = array('respuesta'=>'', 'attachments'=>null);
        if($this->numeroVeces==1) {
            $respuesta['respuesta'] = $this->FormatoTexto();
        }
        else
        {
            $arrAtt=array();
            $result = $this->FormatoAttachment();
            $respuesta['respuesta'] = $result['pretext'];
            $result['pretext']='';
            array_push($arrAtt, $result);
            $respuesta['attachments'] = $arrAtt;
        }
        return $respuesta;

    }
    /**
     * @param $i
     * @return string
     */
    protected function TiradaALinea($i)
    {
        $title = 'Tirada #' . ($i + 1);

        $value = '[' . $this->frase . ']: [';
        for ($k = 0; $k < count($this->dados[$i]); $k++) {
            if ($k != 0) {
                $value .= ', ';
            }
            $value .= $this->dados[$i][$k];
        }
        $value .= '] Suma=[' . $this->suma[$i] . ']';
        if ($this->repetir != 0) {
            $value .= ' (Se repitieron resultados de ' . $this->repetir . ' o menos)';
        }
        return array('title'=>$title,'value'=>$value,'short'=>true);


    }
    protected function FormatoAttachment()
    {
        if($this->tiradaOk)
        {
            $resultado = array(
                'fallback' => $this->FormatoTexto(),
                'pretext' => 'Se realizaron [' . $this->numeroVeces . '] tiradas.',
                'color' => '#ff6600',
                'fields' => array()
            );

            for($i=0; $i< count($this->dados);$i++) {

                array_push($resultado['fields'],$this->TiradaALinea($i));
            }
        }
        else
        {
            $resultado = array(
                'fallback' => 'Error en la tirada: ['.$this->mensajeError.']. Tirada original: '.$this->expresion,
                'pretext' => 'Error en la tirada: ['.$this->mensajeError.']. Tirada original: '.$this->expresion,
                'color' => '#ff6600',
                'fields' => array()
            );
        }

        return $resultado;
       /* $attachments = array();
       [
            'fallback' => 'Lorem ipsum',
            'pretext'  => 'Lorem ipsum',
            'color'    => '#ff6600',
            'fields'   => array(
                [
                    'title' => 'Title',
                    'value' => 'Lorem ipsum',
                    'short' => true
                ],
                [
                    'title' => 'Notes',
                    'value' => 'Lorem ipsum',
                    'short' => true
                ]
            )
        ]);
    */

    }
    /**
     * @return string
     */
    protected function FormatoTexto()
    {
        $resultado = '';
        if(!$this->tiradaOk)
        {
            $resultado = 'Error en la tirada: ['.$this->mensajeError.']. Tirada original: '.$this->expresion;
        }
        else
        {

            if($this->numeroVeces!=1)
            {
                $resultado.='Se realizaron ['.$this->numeroVeces.'] tiradas. ';
            }
            for($i=0; $i< count($this->dados);$i++)
            {
                if($this->numeroVeces!=1)
                {
                    $resultado.='Tirada #'.($i+1);
                }
                $resultado.= ' ['.$this->frase.']: [';
                for($k=0;$k<count($this->dados[$i]);$k++)
                {
                    if($k!=0){ $resultado.=', '; }
                    $resultado.=$this->dados[$i][$k];
                }
                $resultado.='] Suma=[' . $this->suma[$i] . ']';
                if($this->repetir!=0)
                {
                    $resultado.=' (Se repitieron resultados de '.$this->repetir.' o menos)';
                }

            }
        }
        return $resultado;
    }
}


class TiradorDados
{
    /**
     * @param $cad
     * @return RespuestaDados
     * @internal param $cadena : texto especificando la tirada. Sintaxis:
     *      $cadena     := [opciones]*fraseTirada
     *      opciones    := opcion separador
     *      separador   := ;
     *                     ,
     *                     :
     *                     |
     *      opcion      := Nv=digito   -- hacer "digito" tiradas
     *                  := Rp=digito   -- repetir aquellos dados cuyo valor sea menor que "digito"
     *      fraseTirada := digito
     *                     digito D digito
     *                     fraseTirada + fraseTirada
     *                     fraseTirada - fraseTirada
     */
    public function TirarDados($cad)
    {

        $cadena = str_replace('|',',',strtoupper($cad));
        $cadena = str_replace(';',',',$cadena);
        $cadena = str_replace(':',',',$cadena);
        $cadena = str_replace(' ','',$cadena);
        $cadena = str_replace(' ','+',$cadena);
        $nv=1;      // Número de veces a tirar
        $rp=0;      // Valor menor o igual al que repetir
        $frase='';  // Frase de tirada de dados a procesar


        $opciones = explode(',',$cadena);
        for($op=0; $op<count($opciones); $op++)
        {
            $opciones[$op]=trim($opciones[$op]);
            if(substr($opciones[$op],0,2)=='NV')
            {
                $nv = intval(str_replace('NV=','',$opciones[$op]));
            }
            else
                if(substr($opciones[$op],0,2)=='RP')
                {
                    $rp = intval(str_replace('RP=','',$opciones[$op]));
                }
                else
                {
                    $frase =$opciones[$op];
                }
        }

        $resultado = new RespuestaDados();
        $resultado->expresion = $cad;
        $resultado->numeroVeces = $nv;
        $resultado->repetir = $rp;
        $resultado->tiradaOk=true;
        $resultado->dados = array();
        $resultado->suma= array();
        $resultado->mensajeError='';
        $resultado->frase = $frase;



        for($i=0;$i<$nv;$i++)
        {
            $valor = 0;
            $tiradas = array();

            $toExplode = str_replace('-','+',$resultado->frase);

            $tokens = explode('+',$toExplode);
            $suma=0; $fraseTrabajo = $resultado->frase; $operador = '+';
            for($j=0;$j<count($tokens);$j++)
            {
                $valor=0;
                if(is_numeric($tokens[$j]))
                {
                    $valor = $tokens[$j];
                }
                else
                {
                    $partes = explode("D",$tokens[$j]);
                    if(count($partes)!=2)
                    {
                        $resultado->tiradaOk=false;
                        $resultado->mensajeError='Error de Sintaxis: ('. $tokens[$j].') no es del formato (4D6)';
                    }
                    elseif(!is_numeric($partes[0]))
                    {
                        $resultado->tiradaOk=false;
                        $resultado->mensajeError='Error de Sintaxis: ('. $partes[0].') no es un número';
                    }
                    elseif(!is_numeric($partes[1]))
                    {
                        $resultado->tiradaOk=false;
                        $resultado->mensajeError='Error de Sintaxis: ('. $partes[1].') no es un número';
                    }
                    elseif($rp>=intval($partes[1]))
                    {
                        $resultado->tiradaOk=false;
                        $resultado->mensajeError='Error de Semántica: No se puede tirar ('. $tokens[$j].')';
                        $resultado->mensajeError.='Con un mínimo de ('.($rp+1).'), fijado por el parámetro Rp';

                    }
                    if($resultado->tiradaOk)
                    {
                        for($k=0;$k<$partes[0];$k++)
                        {
                            $tirada =rand(1,$partes[1]);
                            while(($rp > 0)&&($tirada <= $rp))
                            {
                                $tirada =rand(1,$partes[1]);
                            }
                            array_push($tiradas,$tirada);
                            $valor+= $tirada;
                        }
                    }

                }
                // valor del token determinado en $valor

                if($operador=='+')
                {
                    $suma+=$valor;
                }
                else
                {
                    $suma-=$valor;
                }

                $fraseTrabajo = substr($fraseTrabajo,strlen($tokens[$j]),strlen($fraseTrabajo)-strlen($tokens[$j]));
                if($fraseTrabajo!='')
                {
                    $operador = substr($fraseTrabajo,0,1);
                    $fraseTrabajo = substr($fraseTrabajo,1,strlen($fraseTrabajo)-1);
                }

            }
            array_push($resultado->dados,$tiradas);
            array_push($resultado->suma,$suma);
        }

        return $resultado;
    }

}
/*
$tirador = new TiradorDados();

echo $tirador->TirarDados($_REQUEST['text'])->FormatoTexto();

?>
*/