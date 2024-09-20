
M.block_yakitory = {

    init: function() {
        
    },
    
    CopyToClipboard: function(url) {
        var input = document.createElement("textarea");
        input.textContent = url;
        input.setAttribute("readonly", "true");
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        input.remove();
    }
};
