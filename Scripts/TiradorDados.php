<?php

/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 28-09-2015
 * Time: 12:51
 */
class TiradorDados
{
    /**
     * @param $cadena: texto especificando la tirada. Sintaxis:
     *  $cadena =    DigitoExpr
     *  Digito = digito;|   <-- cuántas tiradas de dados
     *  Expr =   digito
     *         | digitoDdigito
     *         | Expr + Expr
     *         | Expr - Expr
     */
    public function TirarDados($cadena)
    {
        $veces = 0; $resultado = '';
        $primerPaso = explode(';',strtoupper($cadena));
        $parser = '';



        if(count($primerPaso)==1)
        {
            $veces=1;
            $parser = $primerPaso[0];
        }
        else
        {
            $veces = $primerPaso[0];
            $parser = $primerPaso[1];
        }
        for($i=0;$i<$veces;$i++)
        {
            $valor = 0;

            $toExplode = str_replace('-','+',$parser);
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
                    if(count($partes)!=2){ $valor='Error de Sintaxis';}
                    elseif(!is_numeric($partes[0])){ $valor='Error de Sintaxis';}
                    elseif(!is_numeric($partes[1])){ $valor='Error de Sintaxis';}
                    for($k=0;$k<$partes[0];$k++)
                    {
                        $valor+=rand(1,$partes[1]);
                    }
                }
            }

            if(strlen($resultado!=0))
            {
                $resultado .= ', ';
            }
            $resultado .= $valor;
        }
        $resultado = 'Tirada de: '.$cadena.'.  Resultado: '.$resultado;
        return $resultado;
    }

}
$tirador = new TiradorDados();
echo $tirador->TirarDados($_REQUEST['text']);