// Pour fixer header avec la page on scrolling

var header = document.getElementById("myHeader");

var sticky = header.offsetTop;

window.onscroll = function() 
{

	if (window.pageYOffset > sticky) 
		header.classList.add("sticky");
	else
		header.classList.remove("sticky");
	/*if (window.scrollY  >= window.innerHeight * document.body.clientHeight )
	{
		alert("ldfgd");
	}*/
	//alert(window.scrollY + " "  + window.innerHeight + " " + document.body.clientHeight);
};
