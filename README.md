# Schedule Component

Run the task on shcedule using PHP. This is not cron job or cron tab but it is the schedule tasks using PHP.<br>
You may need cron job or not, in case that there is no cron job just include it in the main PHP file that always called when users visited the webpage.

[![Latest Stable Version](https://poser.pugx.org/rundiz/php-scheduler/v/stable)](https://packagist.org/packages/rundiz/php-scheduler)
[![License](https://poser.pugx.org/rundiz/php-scheduler/license)](https://packagist.org/packages/rundiz/php-scheduler)
[![Total Downloads](https://poser.pugx.org/rundiz/php-scheduler/downloads)](https://packagist.org/packages/rundiz/php-scheduler)

## Example:

```php
$PhpSchedule = new \Rundiz\PhpSchedule\PhpSchedule();
$PhpSchedule->add('unique-name', 'https://mysite.localhost/page/to/works', ['12', '15']);// run on 12 (midday) and 15.
$PhpSchedule->run();
```

Or you can run task using your function/class.

```php
$PhpSchedule = new \Rundiz\PhpSchedule\PhpSchedule();
$PhpSchedule->add('unique-name1', 'myFunction', ['12', '15']);// run on 12 (midday) and 15.
$PhpSchedule->add('unique-name2', ['myStaticClass', 'myStaticMethod'], ['12', '15']);// run on 12 (midday) and 15.
$MyClass = new MyClass();
$PhpSchedule->add('unique-name3', [$MyClass, 'myMethod'], ['12', '15']);// run on 12 (midday) and 15.
$PhpSchedule->run();
```

---

For more example, please look inside **tests** folder.
