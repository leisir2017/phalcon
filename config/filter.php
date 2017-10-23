<?php

# volt模板过滤器
if(!$volt) return null;

$volt->getCompiler()->addFilter('isset', 'isset');
$volt->getCompiler()->addFilter('empty', 'empty');
$volt->getCompiler()->addFilter('count', 'count');
$volt->getCompiler()->addFilter('floatval', 'floatval');
$volt->getCompiler()->addFilter('strstr', 'strstr');

$volt->getCompiler()->addFilter('setdate',function ($resolvedArgs, $exprArgs) {
    return 'date("Y-m-d", '. $resolvedArgs  . ')';
});

$volt->getCompiler()->addFilter('setmonth',function ($resolvedArgs, $exprArgs) {
    return 'date("m-d", '. $resolvedArgs  . ')';
});

$volt->getCompiler()->addFilter('setdatetime',function ($resolvedArgs, $exprArgs) {
    return 'date("Y-m-d H:i:s", '. $resolvedArgs  . ')';
});

$volt->getCompiler()->addFilter('settime',function ($resolvedArgs, $exprArgs) {
    return 'date("H:i:s", '. $resolvedArgs  . ')';
});