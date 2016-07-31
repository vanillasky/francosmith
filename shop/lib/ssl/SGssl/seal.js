function cert_status(code,dom)
{
    var urlname = "http://sgssl.net/cgi-bin/cert-seal4?code="+ code + "&dom="+ dom;
    window.open(urlname, "cert_status","height=600,width=550, menubar=no,directories=no,resizable=no,status=no,scrollbars=yes");

    return;
}
