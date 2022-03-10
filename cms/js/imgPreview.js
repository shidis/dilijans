$(document).ready(function ()
{	
	/* CONFIG */
		
		xOffset = 10;
		yOffset = 30;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		
	/* END CONFIG */
	$("a.iPreview").click(function(){
		return false;
	});
	
	$("a.iPreview").hover(function(e){
		this.t = this.title;
		this.title = "";	
		var c = (this.t != "") ? "<br/>" + this.t : "";
		$("body").append("<p id='iPreview'><img src='"+ this.href +"' alt='Image Preview' />"+ c +"</p>");								 
		$("#iPreview")
			.css({
				position:'absolute',
				border:'1px solid #ccc',
				background:'#333',
				padding:'5px',
				display:'none',
				color:'#fff'
			})
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");						
	},
	function(){
		this.title = this.t;	
		$("#iPreview").remove();
	});	
	$("a.iPreview").mousemove(function(e){
		$("#iPreview")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
	
});