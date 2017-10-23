<?php
# 多文件包含了此文件。各模块Module.php的volt对象包含 ，services.php中过滤器服务包含
use Phalcon\Filter;

# volt模板过滤器
if ( isset($volt) ) {

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
}

# PHP过滤器
if ( isset($filter) ) {

	# 金额，保留两位数
	$filter->add(
	    'priceformat',
	    function ($value) {
	        return round($value,2);
	    }
	);
}
