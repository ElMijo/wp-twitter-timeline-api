/*!
* Tim (lite)
*   github.com/premasagar/tim
*//*
    A tiny, secure JavaScript micro-templating script.
*/
var tim=function(){var e=/{{\s*([a-z0-9_][\\.a-z0-9_]*)\s*}}/gi;return function(f,g){return f.replace(e,function(h,i){for(var c=i.split("."),d=c.length,b=g,a=0;a<d;a++){b=b[c[a]];if(b===void 0)throw"tim: '"+c[a]+"' not found in "+h;if(a===d-1)return b}})}}();

(function(_w,$){

    var WTwitterTimenline = {id:"#wtwitter-root"};

    WTwitterTimenline.templates = {
        wttaheader:'<img src="{{imgurl}}" /><span><span class="wtwitter-username">{{name}}</span><span class="wtwitter-screenname">@{{sname}}</span></span>',
        wttatweet:'<li><p class="wtwitter-text">{{text}}</p></li>',
        wttaauthor:'<a class="wtwitter-author" href="https://twitter.com/{{sname}}/status/{{tweetid}}">@{{sname}}</a>',
        wttahashtag:'<a class="wtwitter-hashtag" href="https://twitter.com/hashtag/{{hashtag}}?src=hash">{{hashtagname}}</a>',
        wttaimage:'<a class="wtwitter-media" href="{{expandedurl}}"><img class="wtwitter-media-source" src="{{mediaurl}}:small" alt="Enlace permanente de imagen incrustada"></a>'
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

    WTwitterTimenline.getTweetsData = function(tweetsArr){

        var tweets = [];

        for($i=0;$i<tweetsArr.length;$i++)
        {
            //ar text = this.tweetLinks(tweetsArr[$i]);

            tweets.push({text:this.tweetLinks(tweetsArr[$i])});

            //console.log(text)
            //var text = tweetsArr[$i].text;

            //tweets.push({text:this.tweetAuthorLink(tweetsArr[$i])});
        }

        return tweets;

/*        return {
            imgurl:userObj.profile_image_url,
            name:userObj.name,
            sname:userObj.screen_name
        };*/

    };


    WTwitterTimenline.renderTweets = function(tweetsArr){

        var tweetsArr = this.getTweetsData(tweetsArr);
        var tweetsList = '';

        console.log(tweetsArr)

        for($i=0;$i<tweetsArr.length;$i++)
        {
            tweetsList+= this.getTemplate('wttatweet',tweetsArr[$i])
        }

        console.log(tweetsList)
        $(this.id+' .wtwitter-timeline-stream ol').html(tweetsList);
    };


/*
                <li>
                    <div class="tweet-header"></div>
                    <div class="tweet-content">
                        <p class="tweet-content-text"></p>
                        <div class="tweet-inline-media"></div>
                    </div>
                    <div class="tweet-footer"></div>
                </li>

 */



    var wtta = new _w.WTwitter();

    wtta
        .on('success',function(rsp){
            console.log(rsp.data);
            if(!rsp.error&&!!rsp.data.length)
            {
                WTwitterTimenline.renderHeader(rsp.data[0].user);
                WTwitterTimenline.renderTweets(rsp.data);
            }
        })
        .getTimeline()
    ;

})(window,jQuery)


/*        $('<img/>',{src:imgurl}).appendTo(header);
        $('<span/>',{
            append:[
                $('<span/>',{class:'wtwitter-username',text:name}),
                $('<span/>',{class:'wtwitter-screenname',text:'@'+screen_name})
            ]
        }).appendTo(header);*/
        // $('<span/>',{class:'wtwitter-username',text:name}).appendTo(header);
        // $('<span/>',{class:'wtwitter-screenname',text:'@'+screen_name}).appendTo(header);
