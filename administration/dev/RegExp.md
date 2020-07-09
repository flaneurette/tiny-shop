# Regular Expressions.

Used to transform variables from JSON and CSV, to speed up programming.

# Convert JSON vars into PHP vars.
```
"(.*)\.(.*)"\:\s+\"\"\,

"\1_\2" => "{\$this->\1_\2}", 
```
Example:
```
"test.id": 1,
```
Result:
```
"test_id" => "{$this->test_id}",
```

# Turn PHP vars into Foreach list keys RegExp.
```
(.*)"(.*)"\s+=>\s+"\{\$this->(.*)\}"\,

\1 $list[$key]['\2'] = "{$this->\2}";
```
Example:
```
"test" => "{$this->test}",
```
Result:
```
$list[$key]['test'] =  "{$this->test}";
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
isset($_POST['test']) ? $this->test = $this->cleanInput($_POST['test']) : $test = false; 
```
