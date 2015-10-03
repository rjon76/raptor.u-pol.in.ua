<h1>{$email.ea_subject} <a href="{$ADMIN_DIR}/mailer/archive/receiver/{$email.ea_email_to}/date/{$date}/" style="font-size:16px;">&lt;- Back</a></h1>
<fieldset>
<table border="0" cellpadding="4" cellspacing=="0">
    <tr>
        <td width="100"><strong>Receiver:</strong></td>
        <td>{$email.ea_email_to}</td>
    </tr>
    <tr>
        <td><strong>Sender:</strong></td>
        <td>{$email.ea_email_from}</td>
    </tr>
    <tr>
        <td><strong>Date:</strong></td>
        <td>{$email.ea_date}</td>
    </tr>
    <tr>
        <td valign="top"><strong>Message:</strong></td>
        <td><pre>{$email.ea_message}</pre></td>
    </tr>
</table>
</fieldset>