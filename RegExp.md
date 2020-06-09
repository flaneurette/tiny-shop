# Regular Expressions.

Used to transform variables from JSON and CSV, to speed up programming.

# Convert JSON vars into PHP vars.
```
"(.*)"\: \"\"\,

"\1" => "{\$this->\1 }",

```
# Turn PHP vars into Foreach list keys RegExp.
```
\"product\_(.*)\"\s+\=>\s+\"\{\$this->product_(.*)\}\"\,


$list[$key]['product_\1'] = "{$this->product_\1}";
```
