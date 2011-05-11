<div id="treepanes" style="overflow-y: auto;">
	<ul id="TreeActions">
		<li class="action" id="addgroup"><button><% _t('CREATE','Create') %></button></li>
	</ul>
	<div style="clear:both;"></div>
	<form class="actionparams" id="addgroup_options" style="display: none" action="admin/security/addgroup">
		<input type="hidden" name="ParentID" />
		<input type="hidden" name="SecurityID" value="$SecurityID" />
		<input class="action" type="submit" value="<% _t('GO','Go') %>" />
	</form>
</div>