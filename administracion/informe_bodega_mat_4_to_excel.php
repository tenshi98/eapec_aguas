<?php
/**********************************************************************************************************************************/
/*                                                     Se llama la libreria                                                       */
/**********************************************************************************************************************************/
require 'lib_PhpOffice/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
/**********************************************************************************************************************************/
/*                                           Se define la variable de seguridad                                                   */
/**********************************************************************************************************************************/
define('XMBCXRXSKGC', 1);
/**********************************************************************************************************************************/
/*                                          Se llaman a los archivos necesarios                                                   */
/**********************************************************************************************************************************/
require_once '../AA2D2CFFDJFDJX1/xrxs_configuracion/config.php';
require_once '../AA2D2CFFDJFDJX1/xrxs_configuracion/conexion.php';
require_once '../AA2D2CFFDJFDJX1/xrxs_funciones/functions.php';
require_once '../AA2D2CFFDJFDJX1/xrxs_funciones/componentes.php';
// obtengo puntero de conexion con la db
$dbConn = conectar();
/**********************************************************************************************************************************/
/*                                                          Consultas                                                             */
/**********************************************************************************************************************************/
//verifico que sea un administrador
if($_GET['tipo']=='SuperAdmin'){
	$z = " WHERE productos_listado.idRubro>=0";
}else{
	$z = " WHERE productos_listado.idRubro=".$_GET['idRubro']." OR productos_listado.idRubro=1";
}

// Se trae un listado con todos los productos
$arrProductos = array();
$query = "SELECT
productos_listado.StockLimite,
productos_listado.Nombre AS NombreProd,
productos_tipo_producto.Nombre AS tipo_producto,
productos_uml.Nombre AS UnidadMedida,
(SELECT SUM(Cantidad_ing) FROM bodegas_facturacion_existencias WHERE idProducto = productos_listado.idProducto AND idBodega=".$_GET['idBodega']." ) AS stock_entrada,
(SELECT SUM(Cantidad_eg) FROM bodegas_facturacion_existencias WHERE idProducto = productos_listado.idProducto AND idBodega=".$_GET['idBodega'].") AS stock_salida,
(SELECT Nombre FROM bodegas_listado WHERE idBodega=".$_GET['idBodega'].") AS NombreBodega

FROM `productos_listado`
LEFT JOIN `productos_uml`              ON productos_uml.idUml                      = productos_listado.idUml
LEFT JOIN `productos_tipo_producto`    ON productos_tipo_producto.idTipoProducto   = productos_listado.idTipoProducto
".$z."
ORDER BY productos_listado.Nombre ASC";
$resultado = mysqli_query ($dbConn, $query);
while ( $row = mysqli_fetch_assoc ($resultado)) {
array_push( $arrProductos,$row );
}

/**********************************************************************************************************************************/
/*                                                          Ejecucion                                                             */
/**********************************************************************************************************************************/
// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator("Office 2007")
							 ->setLastModifiedBy("Office 2007")
							 ->setTitle("Office 2007")
							 ->setSubject("Office 2007")
							 ->setDescription("Document for Office 2007")
							 ->setKeywords("office 2007")
							 ->setCategory("office 2007 result file");


//Titulo columnas
$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Tipo')
            ->setCellValue('B1', 'Nombre')
            ->setCellValue('C1', 'Stock Min')
            ->setCellValue('D1', 'Stock Actual')
            ->setCellValue('E1', 'Unidad de Medida');

$nn=2;
foreach ($arrProductos as $productos) {
	$stock_actual = $productos['stock_entrada'] - $productos['stock_salida'];
	if ($productos['StockLimite']>$stock_actual){
		$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$nn, $productos['tipo_producto'])
					->setCellValue('B'.$nn, $productos['NombreProd'])
					->setCellValue('C'.$nn, cantidades_excel($productos['StockLimite']))
					->setCellValue('D'.$nn, cantidades_excel($stock_actual))
					->setCellValue('E'.$nn, $productos['UnidadMedida']);
		$nn++;
	}
}

// Rename worksheet
$spreadsheet->getActiveSheet(0)->setTitle('Bodega '.$arrProductos[0]['NombreBodega']);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

/**************************************************************************/
//Nombre del archivo
$filename = 'Stock Critico Bodega '.$arrProductos[0]['NombreBodega'].' al '.fecha_actual();
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
