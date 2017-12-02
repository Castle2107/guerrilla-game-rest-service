<h4><b>Assault Reports</b> - Get the specific assault report of a guerrilla</h4>

<p class="label label-success">GET</p>

<kbd>/guerrillas/{guerrilla_id}/assault_reports/{assault_report_id}</kbd>

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
    	<span class="attribute">"status"</span>
        <span class="value">200,</span>
    </p>
    <p class="indent">
    	<span class="attribute">"guerrilla_id":</span>
        <span class="value">"1",</span>
    </p>
    <p class="indent">
    	<span class="attribute">"assault_report":</span>
        <span class="value">{</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"id":</span>
        <span class="value">1,</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"target_id":</span>
        <span class="value">2,</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"attacker_id":</span>
        <span class="value">1,</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"attacker_result_url":</span>
        <span class="value">"storage/5a1b122975155_report.json",</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"target_result_url":</span>
        <span class="value">"storage/5a1b122979ce7_report.json",</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"created_at":</span>
        <span class="value">"2017-11-26 19:12:41",</span>
    </p>
    <p class="indent-double">
    	<span class="attribute">"updated_at":</span>
        <span class="value">"2017-11-26 19:12:41"</span>
    </p>
    <p class="indent">
    	<span class="keyword">}</span>
    </p>
    <p><span class="keyword">}</span></p>
</div>