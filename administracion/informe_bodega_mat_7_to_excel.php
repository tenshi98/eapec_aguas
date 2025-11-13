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
// Se trae un listado con todos los datos
$arrProductos = array();
$query = "SELECT
bodegas_facturacion_existencias.Creacion_fecha,
bodegas_facturacion_existencias.Cantidad_eg,
bodegas_facturacion_tipo.Nombre AS TipoMovimiento,
productos_listado.Nombre AS NombreProducto,
productos_uml.Nombre AS UnidadMedida,
bodegas_documentos_mercantiles.Nombre AS Documento,
bodegas_facturacion.N_Doc AS N_Doc,
clientes_listado.Nombre AS Cliente,
bodegas_listado.Nombre AS NombreBodega

FROM `bodegas_facturacion_existencias`
LEFT JOIN `bodegas_facturacion_tipo`          ON bodegas_facturacion_tipo.idTipo               = bodegas_facturacion_existencias.idTipo
LEFT JOIN `productos_listado`                 ON productos_listado.idProducto                  = bodegas_facturacion_existencias.idProducto
LEFT JOIN `productos_uml`                     ON productos_uml.idUml                           = productos_listado.idUml
LEFT JOIN `bodegas_facturacion`               ON bodegas_facturacion.idFacturacion             = bodegas_facturacion_existencias.idFacturacion
LEFT JOIN `bodegas_documentos_mercantiles`    ON bodegas_documentos_mercantiles.idDocumentos   = bodegas_facturacion.idDocumentos
LEFT JOIN `clientes_listado`                  ON clientes_listado.idCliente                    = bodegas_facturacion.idCliente
LEFT JOIN `bodegas_listado`                   ON bodegas_listado.idBodega                      = bodegas_facturacion_existencias.idBodega

WHERE bodegas_facturacion.idCliente=".$_GET['idCliente']."
AND bodegas_facturacion_existencias.Creacion_fecha BETWEEN '".$_GET['f_inicio']."' AND '".$_GET['f_termino']."'
ORDER BY bodegas_facturacion_existencias.Creacion_fecha DESC ";
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
            ->setCellValue('A1', 'Cliente: '.$arrProductos[0]['Cliente']);
//Titulo columnas
$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Registros entre fechas '.Fecha_estandar($_GET['f_inicio']).' al '.Fecha_estandar($_GET['f_termino']));

//Titulo columnas
$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A4', 'Bodega')
            ->setCellValue('B4', 'Producto')
            ->setCellValue('C4', 'Movimiento')
            ->setCellValue('D4', 'Documento')
            ->setCellValue('E4', 'Fecha')
            ->setCellValue('F4', 'Cant eg')
            ->setCellValue('G4', 'Unidad de Medida');

$nn=5;
foreach ($arrProductos as $productos) {

    $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$nn, $productos['NombreBodega'])
                ->setCellValue('B'.$nn, $productos['NombreProducto'])
                ->setCellValue('C'.$nn, $productos['TipoMovimiento'])
                ->setCellValue('D'.$nn, $productos['Documento'].' N° '.$productos['N_Doc'])
                ->setCellValue('E'.$nn, Fecha_estandar($productos['Creacion_fecha']))
                ->setCellValue('F'.$nn, cantidades_excel($productos['Cantidad_eg']))
                ->setCellValue('G'.$nn, $productos['UnidadMedida']);
    $nn++;

}

// Rename worksheet
$spreadsheet->getActiveSheet(0)->setTitle('Bodega '.$arrProductos[0]['NombreBodega']);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

/**************************************************************************/
//Nombre del archivo
$filename = 'Movimiento Cliente '.$arrProductos[0]['Cliente'];
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
