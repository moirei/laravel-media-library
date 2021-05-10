(window.webpackJsonp=window.webpackJsonp||[]).push([[4,17,24],{378:function(e,t,n){},379:function(e,t,n){"use strict";n.r(t);n(54),n(79),n(115);var r={name:"DataTable",props:["data"],computed:{headers:function(){return this.data&&this.data.length?Object.keys(this.data[0]).map((function(e){return(t=e).charAt(0).toUpperCase()+t.slice(1);var t})):[]}}},s=n(53),a=Object(s.a)(r,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("table",[n("tr",e._l(e.headers,(function(t){return n("th",{key:t},[e._v(e._s(t))])})),0),e._v(" "),e._l(e.data,(function(t,r){return n("tr",{key:"row-"+r},e._l(t,(function(t){return n("td",{key:t},[e._v(e._s(t))])})),0)}))],2)}),[],!1,null,null,null);t.default=a.exports},380:function(e,t,n){"use strict";n(378)},381:function(e,t,n){"use strict";n.r(t);var r={name:"Endpoint",components:{DataTable:n(379).default},props:{name:String,method:String,endpoint:String,body:{},response:{}},computed:{responseIsLink:function(){var e;return!(null===(e=this.response)||void 0===e||!e.route)},methodClass:function(){return String(this.method).toLowerCase()}}},s=(n(380),n(53)),a=Object(s.a)(r,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",[n("h3",[e._v(e._s(e.name))]),e._v(" "),e.$slots.description?n("p",{staticStyle:{"margin-bottom":"20px"}},[e._t("description")],2):e._e(),e._v(" "),n("p",[e._v("Method: "),n("code",{staticClass:"method",class:[e.methodClass]},[e._v(e._s(e.method))])]),e._v(" "),n("p",[e._v("Endpoint: "),n("code",[e._v("/media-library/"+e._s(e.endpoint))])]),e._v(" "),e.body?[n("h4",[e._v("Body")]),e._v(" "),n("data-table",{attrs:{data:e.body}})]:e._e(),e._v(" "),n("h4",[e._v("Response")]),e._v(" "),e.responseIsLink?[n("router-link",{attrs:{to:e.response.route}},[e._v(e._s(e.response.name))])]:n("data-table",{attrs:{data:e.response}}),e._v(" "),e.$slots.default?n("div",[n("h4",[e._v("Example")]),e._v(" "),e._t("default")],2):e._e()],2)}),[],!1,null,null,null);t.default=a.exports},385:function(e,t,n){"use strict";t.a={body:[{property:"name",type:"string",description:"The name of the resource. Defaults to the file or folder name"},{property:"description",type:"string",description:"The description of the resource"},{property:"public",type:"boolean",description:"Whether the shared content is public"},{property:"access_emails",type:"array",description:"If not public, a list of emails that may authenticate to access content"},{property:"access_keys",type:"array",description:"Required if not public. A list of access codes. Each code must be at least 6-digits"},{property:"access_type",type:"enum",description:"Indicate if the access is a token or secret. Values: `token`, `secret`. The default is `token`"},{property:"expires_at",type:"string (ISO 8601)",description:"Sets an expiry on the resource"},{property:"can_remove",type:"boolean",description:"Indicate whether shared files/folders may be deleted"},{property:"can_upload",type:"boolean",description:"If sharing a folder, whether files may be uploaded"},{property:"can_upload",type:"boolean",description:"If sharing a folder, whether files may be uploaded"},{field:"max_downloads",type:"integer",description:"Limit max downloads"},{field:"allowed_upload_types",type:"array",description:"Limit uploadable mime types"}],response:[{field:"id",type:"string",description:"The shared content resource ID"},{field:"url",type:"string",description:"A url to share with external"}]}},459:function(e,t,n){"use strict";n.r(t);var r=n(381),s=n(385),a={name:"EndpointShareableLink",components:{Endpoint:r.default},data:function(){return{data:s.a}}},o=n(53),i=Object(o.a)(a,(function(){var e=this.$createElement;return(this._self._c||e)("endpoint",this._b({attrs:{name:"Get file shareable link",method:"POST",endpoint:"share/{file-id}"}},"endpoint",this.data,!1),[this._t("default")],2)}),[],!1,null,null,null);t.default=i.exports}}]);