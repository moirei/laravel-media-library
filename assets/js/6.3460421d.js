(window.webpackJsonp=window.webpackJsonp||[]).push([[6,17,24],{378:function(t,e,n){},379:function(t,e,n){"use strict";n.r(e);n(54),n(79),n(115);var a={name:"DataTable",props:["data"],computed:{headers:function(){return this.data&&this.data.length?Object.keys(this.data[0]).map((function(t){return(e=t).charAt(0).toUpperCase()+e.slice(1);var e})):[]}}},s=n(53),r=Object(s.a)(a,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("table",[n("tr",t._l(t.headers,(function(e){return n("th",{key:e},[t._v(t._s(e))])})),0),t._v(" "),t._l(t.data,(function(e,a){return n("tr",{key:"row-"+a},t._l(e,(function(e){return n("td",{key:e},[t._v(t._s(e))])})),0)}))],2)}),[],!1,null,null,null);e.default=r.exports},380:function(t,e,n){"use strict";n(378)},381:function(t,e,n){"use strict";n.r(e);var a={name:"Endpoint",components:{DataTable:n(379).default},props:{name:String,method:String,endpoint:String,body:{},response:{}},computed:{responseIsLink:function(){var t;return!(null===(t=this.response)||void 0===t||!t.route)},methodClass:function(){return String(this.method).toLowerCase()}}},s=(n(380),n(53)),r=Object(s.a)(a,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("h3",[t._v(t._s(t.name))]),t._v(" "),t.$slots.description?n("p",{staticStyle:{"margin-bottom":"20px"}},[t._t("description")],2):t._e(),t._v(" "),n("p",[t._v("Method: "),n("code",{staticClass:"method",class:[t.methodClass]},[t._v(t._s(t.method))])]),t._v(" "),n("p",[t._v("Endpoint: "),n("code",[t._v("/media-library/"+t._s(t.endpoint))])]),t._v(" "),t.body?[n("h4",[t._v("Body")]),t._v(" "),n("data-table",{attrs:{data:t.body}})]:t._e(),t._v(" "),n("h4",[t._v("Response")]),t._v(" "),t.responseIsLink?[n("router-link",{attrs:{to:t.response.route}},[t._v(t._s(t.response.name))])]:n("data-table",{attrs:{data:t.response}}),t._v(" "),t.$slots.default?n("div",[n("h4",[t._v("Example")]),t._v(" "),t._t("default")],2):t._e()],2)}),[],!1,null,null,null);e.default=r.exports},441:function(t,e,n){"use strict";n.r(e);var a=n(381),s={response:{name:"Attachment",route:"/data.html#attachment"}},r={name:"EndpointAttachmentDelete",components:{Endpoint:a.default},data:function(){return{data:s}}},o=n(53),i=Object(o.a)(r,(function(){var t=this.$createElement;return(this._self._c||t)("endpoint",this._b({attrs:{name:"Purge an attachment",method:"DELETE",endpoint:"attachment/{url|attachment-id}"}},"endpoint",this.data,!1),[this._t("default")],2)}),[],!1,null,null,null);e.default=i.exports}}]);