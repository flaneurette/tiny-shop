# Regular Expressions.

Used to transform variables from JSON and CSV, to speed up programming.

# Convert JSON vars into PHP vars.
```
"(.*)"\: \"\"\,

"\1" => "{\$this->\1 }",
````
