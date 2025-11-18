# uBix Core

## Windows CLI

1. Create a $profile script -> notepad $profile
2. Add these contents

``
function UbixCmdlet {
param (
        [string]$Command,
        [Parameter(ValueFromRemainingArguments = $true)]
        [string[]]$Args
    )
    # Do the actual thing
	php "C:\Users\cwols\git\ubixcore\bin\ubix" $Command $($Args -join ' ')
}

Set-Alias ubix UbixCmdlet
``

3. Load the profile -> . $profile

## PHPUnit and unit tests

You can run our test suite by entering the project root and running `vendor/bin/phpunit`

### Code coverage

You can generate a code coverage report by entering the project root and running `vendor/bin/phpunit --coverage-html coverage/`

You will need to install xdebug for the code coverage to work which can be installed by running `sudo pecl install xdebug` and adding `zend_extension=xdebug.so` to your php.ini file (you may also need to add `xdebug.mode = coverage` to your php.ini file)
