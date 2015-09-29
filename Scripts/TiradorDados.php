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
    public  $dados;             // Resultados de cada dado, array de enteros, multidimensional
    public  $suma;              // array de sumas de cada tirada de dados
    public  $tiradaOk;          // true|false
    public  $mensajeError;      // En caso de que haya habido un error

   public function FormatoTexto()
    {

    }
}


class TiradorDados
{
    /**
     * @param $cadena: texto especificando la tirada. Sintaxis:
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
     * @return RespuestaDados
     */
    public function TirarDados($cad)
    {
        $cadena = str_replace('|',',',strtoupper($cad));
        $cadena = str_replace(';',',',$cadena);
        $cadena = str_replace(':',',',$cadena);
        $nv=1;      // Número de veces a tirar
        $rp=0;      // Valor menor o igual al que repetir
        $frase='';  // Frase de tirada de dados a procesar

        $opciones = explode(',',$cadena);

        for($op=0; $op<count($opciones); $op++)
        {
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

        for($i=0;$i<$nv;$i++)
        {
            $valor = 0;
            $tiradas = array();
            $toExplode = str_replace('-','+',$frase);
            $tokens = explode('+',$toExplode);

            for($j=0;$j<count($tokens);$j++)
            {
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
                array_push($resultado->dados,$tiradas);
                array_push($resultado->suma,$valor);
            }

        }
        return $resultado;
    }

}
$tirador = new TiradorDados();
var_dump($tirador->TirarDados($_REQUEST['text']));

