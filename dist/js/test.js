var Test = {
	
	print: function() {
		console.log("hello world");
	},
	
	bar: function() {
		console.log("baz");
		Dispatcher.emitEvent("test");
	}
	
}