var PluginKSS = {
	
	insert: function(patternData) {
		
		console.log("trying to insert");
		
		var name = "pattern-lab-plugin-kss-templates-code-insert";
		
		var template         = document.getElementById(name+"-template");
		var templateCompiled = Hogan.compile(template.innerHTML);
		var templateRendered = templateCompiled.render(patternData.extraOutput["pattern-lab-plugin-kss"]);
		
		var p       = document.getElementById('sg-code-extra-output');
		var n       = document.createElement('div');
		n.id        = name;
		n.innerHTML = templateRendered;
		p.parentNode.insertBefore(n, p.nextSibling);
		
	}
	
}