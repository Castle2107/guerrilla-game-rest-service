<h4><b>Inspect Guerrilla</b> - Inspect a guerrilla</h4>

<p class="label label-primary">POST</p>

<kbd>/inspect_guerrilla</kbd>

<!-- Request -->
<h5><b>Request Body</b></h5>
<h6><b>By ID</b></h6>
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
        <span class="attribute">"id":</span>
        <span class="value">100</span>
    </p>
    <p><span class="keyword">}</span></p>
</div>

<h6><b>By username</b></h6>
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
        <span class="value">"ameseguer"</span>
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
    <p><span class="keyword">{</span></p>
    <p class="indent">
        <span class="attribute">"id":</span>
        <span class="value">100</span>
    </p>
    <p class="indent">
        <span class="attribute">"faction":</span>
        <span class="value">"USMC",</span>
    </p>
    <p class="indent">
        <span class="attribute">"username":</span>
        <span class="value">"ameseguer",</span>
    </p>
    <p class="indent">
        <span class="attribute">"ranking":</span>
        <span class="value">3,</span>
    </p>
    <p class="indent">
            <span class="attribute">"points":</span>
            <span class="value">2000,</span>
        </p>
        <p class="indent">
            <span class="attribute">"timestamp":</span>
            <span class="value">-4679715600,</span>
        </p>
        <p class="indent">
            <span class="attribute">"email":</span>
            <span class="value">"andres.meseguer@ucr.ac.cr",</span>
        </p>
        <p class="indent">
            <span class="attribute">"resources":</span>
            <span class="keyword">{</span>
            <p class="indent-double">
                <span class="attribute">"oil":</span>
                <span class="value">100,</span>
            </p>
            <p class="indent-double">
                <span class="attribute">"money":</span>
                <span class="value">500,</span>
            </p>
            <p class="indent-double">
                <span class="attribute">"people":</span>
                <span class="value">300</span>
            </p>
            <p class="indent-double">
                <span class="keyword">},</span>
            </p>
        </p>
        <p class="indent">
            <span class="attribute">"defense":</span>
            <span class="keyword">{</span>
            <p class="indent-double">
                <span class="attribute">"bunkers":</span>
                <span class="value">2</span>
            </p>
            <p class="indent-double">
                <span class="keyword">},</span>
            </p>
        </p>
        <p class="indent">
            <span class="attribute">"offense":</span>
            <span class="keyword">{</span>
            <p class="indent-double">
                <span class="attribute">"assault":</span>
                <span class="value">1,</span>
            </p>
            <p class="indent-double">
                <span class="attribute">"engineers":</span>
                <span class="value">2,</span>
            </p>
            <p class="indent-double">
                <span class="attribute">"tanks":</span>
                <span class="value">4</span>
            </p>
            <p class="indent">
                <span class="keyword">}</span>
            </p>
    <p><span class="keyword">}</span></p>
</div>