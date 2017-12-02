<h4><b>Buy Guerrilla</b> - Buys the battle units specified by a guerrilla</h4>

<p class="label label-primary">POST</p>

<kbd>/buy_guerrilla</kbd>

<!-- Request -->
<h5><b>Request Body</b></h5>
<div class="relevant-content">
    <p>
        <span class="attribute">Content-Type:</span>
        <span class="value">application/json</span>
    </p>
    <p>
        <span class="attribute">Accept:</span>
        <span class="value">application/json</span>
    </p>
    <p><span class="keyword">{</span></p>
    <p class="indent">
        <span class="attribute">"username":</span>
        <span class="value">"ameseguer",</span>
    </p>
    <p class="indent">
        <span class="attribute">"defense":</span>
        <span class="keyword">{</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"bunkers":</span>
        <span class="keyword">2</span>
    </p>
    <p class="indent">
    	<span class="keyword">},</span>
    </p>
    <p class="indent">
        <span class="attribute">"offense":</span>
        <span class="keyword">{</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"assault":</span>
        <span class="keyword">1,</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"engineers":</span>
        <span class="keyword">2,</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"tanks":</span>
        <span class="keyword">4</span>
    </p>
    <p class="indent">
    	<span class="keyword">}</span>
    </p>
    <p><span class="keyword">}</span></p>
</div>

<!-- Response -->
<h5><b>Success Response</b></h5>
<div class="relevant-content">
	<p>
        <span class="attribute">Access-Control-Allow-Origin:</span>
        <span class="value">*</span>
    </p>
    <p>
        <span class="attribute">Content-Type:</span>
        <span class="value">application/json</span>
    </p>
    <p>
        <span class="attribute">Date:</span>
        <span class="value">Fri, 01 Dec 2017 20:51:33 +0000, Fri, 01 Dec 2017 20:51:33 GMT</span>
    </p>
    <p>
        <span class="attribute">Host:</span>
        <span class="value">localhost:8000</span>
    </p>
</div>