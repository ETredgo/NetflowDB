<?php

  // Path to the python script - either FULL path or relative to PHP script
  $pythonScript = '/var/netflowdb/netflowdb.py';

  // Path to python executable  - either FULL path or relative to PHP script
  $pythonExec = '/usr/bin/python';

  // Check the file exists and PHP has permission to execute it
  clearstatcache();
  if (!file_exists($pythonExec)) {
    exit("The python executable '$pythonExec' does not exist!");
  }
  if (!is_executable($pythonExec)) {
    exit(("The python executable '$pythonExec' is not executable!"));
  }
  if (!file_exists($pythonScript)) {
    exit("The python script file '$pythonScript' does not exist!");
  }

  // Execute it, and redirect STDERR to STDOUT so we can see error messages as well
  exec("$pythonExec \"$pythonScript\" 2>&1", $output);

  // Show the output of the script
  print_r($output);

?>
