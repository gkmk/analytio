

	<p>Enter captcha code: <input id="the_vault_code" type="text" name="the_vault_code" size="12" /></p>

      <img id="siimage" align="left" style="margin-top:2px; padding-right: 5px; border: 0" src="capture/securimage_show.php?sid=<?php echo md5(time()); ?>" />

        <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="19" height="19" id="SecurImage_as3" align="middle">
			    <param name="allowScriptAccess" value="sameDomain" />
			    <param name="allowFullScreen" value="false" />
                <param name="wmode" value="opaque" />
			    <param name="movie" value="capture/securimage_play.swf?audio=capture/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5" />
			    <param name="quality" value="high" />
			
			    <param name="bgcolor" value="#ffffff" />
			    <embed src="capture/securimage_play.swf?audio=capture/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5" quality="high" bgcolor="#ffffff" wmode="opaque" width="19" height="19" name="SecurImage_as3" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			  </object>

        <br />
        
        <!-- pass a session id to the query string of the script to prevent ie caching -->
        <a tabindex="-1" style="border-style: none" title="Refresh Image" onClick="document.getElementById('siimage').src = 'capture/securimage_show.php?sid=' + Math.random(); return false"><img src="capture/images/refresh.gif" alt="Reload Image" border="0" onClick="this.blur()" align="bottom" /></a>
