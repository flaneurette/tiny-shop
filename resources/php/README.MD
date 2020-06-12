
# SimpleXLSX php class v0.8.15

MS Excel 2007 workbooks reader
 
Copyright (c) 2012 - 2020 SimpleXLSX
```
@category SimpleXLSX
@packageSimpleXLSX
@copyrightCopyright (c) 2012 - 2020 SimpleXLSX (https://github.com/shuchkin/simplexlsx/)
@licenseMIT
@version0.8.15
```

# Examples
 
Example 1:
```
if ( $xlsx = SimpleXLSX::parse('book.xlsx') ) {
print_r( $xlsx->rows() );
} else {
echo SimpleXLSX::parseError();
}
```
Example 2: 
```
html table
if ( $xlsx = SimpleXLSX::parse('book.xlsx') ) {
echo $xlsx->toHTML();
} else {
echo SimpleXLSX::parseError();
}
```
Example 3: rowsEx
```
$xlsx = SimpleXLSX::parse('book.xlsx');
print_r( $xlsx->rowsEx() );
```
Example 4: select worksheet
```
$xlsx = SimpleXLSX::parse('book.xlsx');
print_r( $xlsx->rows(1) ); // second worksheet
 ```
Example 5: IDs and worksheet names
```
$xlsx = SimpleXLSX::parse('book.xlsx');
print_r( $xlsx->sheetNames() ); // array( 0 => 'Sheet 1', 1 => 'Catalog' );
```
Example 6: get sheet name by index
```
$xlsx = SimpleXLSX::parse('book.xlsx');
echo 'Sheet Name 2 = '.$xlsx->sheetName(1);
```
Example 7: getCell (very slow)
```
echo $xlsx->getCell(1,'D12'); // reads D12 cell from second sheet
```
Example 8: read data
```
if ( $xlsx = SimpleXLSX::parseData( file_get_contents('http://www.example.com/example.xlsx') ) ) {
$dim = $xlsx->dimension(1);
$num_cols = $dim[0];
$num_rows = $dim[1];
echo $xlsx->sheetName(1).':'.$num_cols.'x'.$num_rows;
} else {
echo SimpleXLSX::parseError();
}
```
Example 9: old style
```
$xlsx = new SimpleXLSX('book.xlsx');
if ( $xlsx->success() ) {
print_r( $xlsx->rows() );
} else {
echo 'xlsx error: '.$xlsx->error();
}
```
