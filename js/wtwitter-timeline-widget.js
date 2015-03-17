/*A tiny, secure JavaScript micro-templating script.*/
var tim=function(){var e=/{{\s*([a-z0-9_][\\.a-z0-9_]*)\s*}}/gi;return function(f,g){return f.replace(e,function(h,i){for(var c=i.split("."),d=c.length,b=g,a=0;a<d;a++){b=b[c[a]];if(b===void 0)throw"tim: '"+c[a]+"' not found in "+h;if(a===d-1)return b}})}}();

(function(_w,$){

    var WTwitterTimenline = {id:"#wtwitter-root",obj:null,timer:null};

    WTwitterTimenline.templates = {
        wttaheader:'<img src="{{imgurl}}" /><span><span class="wtwitter-username">{{name}}</span><span class="wtwitter-screenname">@{{sname}}</span></span>',
        wttatweet:'<li><div class="wtwitter-header"><time class="wtwitter-date-time">{{datetime}}</time></div><div class="wtwitter-content"><p class="wtwitter-text">{{text}}</p></div><div class="wtwitter-footer">{{acctions}}</div></li>',
        wttaauthor:'<a class="wtwitter-author" href="https://twitter.com/{{sname}}/status/{{tweetid}}">@{{sname}}</a>',
        wttahashtag:'<a class="wtwitter-hashtag" href="https://twitter.com/hashtag/{{hashtag}}?src=hash">{{hashtagname}}</a>',
        wttaimage:'<a class="wtwitter-media" href="{{expandedurl}}"><img class="wtwitter-media-source" src="{{mediaurl}}:small" alt="Enlace permanente de imagen incrustada"></a>',
        wttabtnseguir:'<a class="wtwitter-button-seguir" target="_blank" href="https://twitter.com/intent/follow?screen_name={{sname}}">Seguir</a>',
        wttabtntwittear:'<a class="wtwitter-button-twittear" target="_blank" href="https://twitter.com/intent/tweet?screen_name={{sname}}">Twittear</a>',
        wtwitteryear:'<span class="wtwitter-year">{{year}}</span>',
        wtwittermonth:'<span class="wtwitter-month">{{month}}</span>',
        wtwittertime:'<span class="wtwitter-day-time" >{{time}}</span>',
        wtwitteracctions:'<a target="_blank" class="wtwitter-icon wtwitter-icon-replay" href="https://twitter.com/intent/tweet?in_reply_to={{tweetid}}"></a><a target="_blank" class="wtwitter-icon wtwitter-icon-retweet" href="https://twitter.com/intent/retweet?tweet_id={{tweetid}}"></a><a target="_blank" class="wtwitter-icon wtwitter-icon-favorite" href="https://twitter.com/intent/favorite?tweet_id={{tweetid}}"></a>'
    };

    WTwitterTimenline.getTemplate = function(template,data){

        return !!this.templates[template]?tim(this.templates[template],data||{}):'';

    };

    WTwitterTimenline.getHeaderData = function(userObj){

        return {
            imgurl:userObj.profile_image_url,
            name:userObj.name,
            sname:userObj.screen_name
        };

    };

    WTwitterTimenline.renderHeader = function(userObj,force){

        var header = $(this.id+' .wtwitter-timeline-header'),force = !!force;

        if(!header.find('img').length||!!force)
        {
            header.html(this.getTemplate('wttaheader',this.getHeaderData(userObj)));
        }

    };

    WTwitterTimenline.getTweetLinksMediaObjects = function(tweetMediaArr,url){

        var tweetMediaObj = {expandedurl:null,mediaurl:null};

        for($m=0;$m<tweetMediaArr.length;$m++)
        {
            if(tweetMediaArr[$m].url==url)
            {
                tweetMediaObj.expandedurl = tweetMediaArr[$m].expanded_url;
                tweetMediaObj.mediaurl = tweetMediaArr[$m].media_url;
            }
        }

        return tweetMediaObj;
    };


    WTwitterTimenline.tweetLinks = function(tweetObj){

        var textArr = tweetObj.text.split(' ');

        for($t=0;$t<textArr.length;$t++)
        {
            if(textArr[$t].indexOf('@')>=0)
            {
                textArr[$t] = this.getTemplate('wttaauthor',{sname:tweetObj.user.screen_name,tweetid:tweetObj.id_str})
            }
            else if(textArr[$t].indexOf('#')>=0)
            {
                textArr[$t] = this.getTemplate('wttahashtag',{hashtag:textArr[$t].replace('#',''),hashtagname:textArr[$t]})
            }
            else if(textArr[$t].indexOf('http')>=0)
            {
                textArr[$t] = this.getTemplate('wttaimage',this.getTweetLinksMediaObjects(tweetObj.entities.media,textArr[$t]));
            }
        }
        return textArr.join(' ');

    };

    WTwitterTimenline.tweetDateTime = function(strDate)
    {
        var months = Array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic');
        var now = new Date(), tweetdate = new Date(strDate), year = month = time = '';

        if(tweetdate.getFullYear() < now.getFullYear())
        {
            year = this.getTemplate('wtwitteryear',{year:tweetdate.getFullYear()});
        }
        if(tweetdate.getMonth() < now.getMonth()||!!String(year).length)
        {
            var month = this.getTemplate('wtwittermonth',{month:months[tweetdate.getMonth()]});
        }

        if(!String(month).length)
        {
            var day = now.getDate() - tweetdate.getDate();
            var hours = now.getHours() - tweetdate.getHours();
            var min = now.getMinutes() - tweetdate.getMinutes();
            var sec = now.getSeconds() - tweetdate.getSeconds();

            if(day > 0)
            {
                time = day+' d';
            }
            else if (hours > 0)
            {
                time = hours+' h';
            }
            else if (min > 0)
            {
                time = min+' m';
            }
            else
            {
                time = sec+' s';
            }
        }
        else
        {
            time = tweetdate.getDate();
        }

        time = this.getTemplate('wtwittertime',{time:time})

        return year+month+time;
    };

    WTwitterTimenline.getTweetsData = function(tweetsArr){

        var tweets = [];

        for($i=0;$i<tweetsArr.length;$i++)
        {
            tweets.push({
                text:this.tweetLinks(tweetsArr[$i]),
                datetime:this.tweetDateTime(tweetsArr[$i].created_at),
                acctions:this.getTemplate('wtwitteracctions',{tweetid:tweetsArr[$i].id_str})
            });
        }

        return tweets;
    };


    WTwitterTimenline.renderTweets = function(tweetsArr){

        var tweetsArr = this.getTweetsData(tweetsArr),tweetsList = '';

        for($i=0;$i<tweetsArr.length;$i++)
        {
            tweetsList+= this.getTemplate('wttatweet',tweetsArr[$i])
        }
        $(this.id+' .wtwitter-timeline-stream ol').html(tweetsList);
    };

    WTwitterTimenline.renderFooter = function(userObj){

        var footer = $(this.id+' .wtwitter-timeline-footer');

        footer
            .html('')
            .append(this.getTemplate('wttabtnseguir',{sname:userObj.screen_name}))
            .append(this.getTemplate('wttabtntwittear',{sname:userObj.screen_name}))
        ;

    };

    WTwitterTimenline.loaderIn = function(){
        $(".wtwitter-loader img").stop(true,true).animate({opacity:0.8},'slow','linear',WTwitterTimenline.loaderOut)
    };

    WTwitterTimenline.loaderOut = function(){
        $(".wtwitter-loader img").stop(true,true).animate({opacity:0.4},1500,'linear',WTwitterTimenline.loaderIn)
    }

    WTwitterTimenline.init = function()
    {
        WTwitterTimenline.obj = new _w.WTwitter();

        WTwitterTimenline.obj
            .on('success',function(rsp){
                if(!rsp.error&&!!rsp.data.length)
                {
                    WTwitterTimenline.renderHeader(rsp.data[0].user);
                    WTwitterTimenline.renderTweets(rsp.data);
                    WTwitterTimenline.renderFooter(rsp.data[0].user);

                    $(".wtwitter-loader img").stop( true, true ).fadeOut('fast',function(){
                        $(this).parent().remove();
                        $("#wtwitter-root").show();
                    });
                }
            })
            .getTimeline()
        ;

        WTwitterTimenline.timer = setInterval(function(){WTwitterTimenline.obj.getTimeline()},10000);
    };

    $(document).ready(function(){

        WTwitterTimenline.loaderOut();

        if(!!_w.Pace)
        {
            Pace.on('hide',WTwitterTimenline.init);
        }
        else
        {
            WTwitterTimenline.init();
        }

    });
})(window,jQuery)