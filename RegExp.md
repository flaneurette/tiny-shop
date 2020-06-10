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
# Turn JSON PHP vars into $_POST evaluation RegExp.
```
(.*)"(.*)"\s+=>\s+"\{\$this->(.*)\}"\,

\1 isset\($_POST['\2']\) ? $this->\2 = $this->cleanInput\($_POST['\2']\) : $\2 = false;  
```
Example:
```
"test" => "{$this->test}",
```
Result:
```
isset($_POST['test'] ? $this->test = $this->cleanInput$_POST['test'] : $test = false; 
```
