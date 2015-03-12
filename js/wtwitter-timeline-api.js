(function(_w,$){

    _w.WTwitter = function(){this.events = {};return this;};

    _w.WTwitter.prototype.getTimeline = function(){
        var self = this;

        var execEvent = function(e,args){

            if(!!this.events[e]&&typeof this.events[e] === "function")
            {
                this.events[e].apply(_w,args);
            }
        };

        var xhr  = $.ajax({
            dataType:"json",
            url:_w.wtta.ajaxurl,
            data:{action:'get_timeline'},
            beforeSend:function(xhr){execEvent.apply(self,['start',[xhr]]);}
        });

        xhr.done(function(data,textStatus,jqXHR){
            execEvent.apply(self,['success',[data,textStatus,jqXHR]]);
        });

        xhr.fail(function(jqXHR,textStatus,errorThrown){
            execEvent.apply(self,['error',[jqXHR,textStatus,errorThrown]]);
        });

        xhr.always(function(data_jqXHR,textStatus,jqXHR_errorThrown){
            execEvent.apply(self,['stop',[data_jqXHR,textStatus,jqXHR_errorThrown]]);
        });

        return this;
    };

    _w.WTwitter.prototype.on = function(e,fn){
        if(['start','error','success','stop'].indexOf(e)>=0)
        {
            this.events[e] = fn;
        }
        return this;
    };

})(window,jQuery)