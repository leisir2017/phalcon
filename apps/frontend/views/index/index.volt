<h1>Welcome to my project!</h1>

<p>项目名称：<?php echo $this->config->site_name ?></p>
<p>根目录：<?php echo $this->url->getBaseUri() ?></p>
<p>资源目录：<?php echo $this->config->site_source ?></p>
<p>接口目录：<?php echo $this->config->site_api ?></p>

{% if info.title|isset %}
	<p>数据：{{info.title}}</p>
{% endif %}
<p>数据：{{info1.title}}</p>