(window.webpackJsonp=window.webpackJsonp||[]).push([[39],{473:function(t,a,s){"use strict";s.r(a);var e=s(53),n=Object(e.a)({},(function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("ContentSlotsDistributor",{attrs:{"slot-key":t.$parent.slotKey}},[s("h1",{attrs:{id:"data-casts"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#data-casts"}},[t._v("#")]),t._v(" Data Casts")]),t._v(" "),s("p",[t._v("This package provides a number of casts to help interface media files to native array or object types. Casts also allows you to use media files at attribute level rather than associating them to your entire model. The stored value in your database column is the file ID or an array on IDs.")]),t._v(" "),s("p",[t._v("Ascribing the "),s("code",[t._v("MOIREI\\MediaLibrary\\Traits\\InteractsWithMedia")]),t._v(" trait will automatically detect and associate files to the model.")]),t._v(" "),s("p",[s("strong",[t._v("Battery-included Casts")])]),t._v(" "),s("table",[s("thead",[s("tr",[s("th",[t._v("Cast")]),t._v(" "),s("th",[t._v("Description")])])]),t._v(" "),s("tbody",[s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaFile")])]),t._v(" "),s("td",[t._v("Casts files to view/frontend ready key-value array. See "),s("a",{attrs:{href:"/data#file"}},[t._v("File")]),t._v(".")])]),t._v(" "),s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaFiles")])]),t._v(" "),s("td",[t._v("An array of "),s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaFile")])])]),t._v(" "),s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaImage")])]),t._v(" "),s("td",[t._v("Key-value array of display image links of the file. Empty for non-image files with no thumbnail generated")])]),t._v(" "),s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaImages")])]),t._v(" "),s("td",[t._v("An array of "),s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaImage")])])]),t._v(" "),s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaObjectImage")])]),t._v(" "),s("td",[t._v("An "),s("code",[t._v("object")]),t._v(" version of "),s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaImage")])])]),t._v(" "),s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaObjectImages")])]),t._v(" "),s("td",[t._v("An array of "),s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaObjectImage")])])]),t._v(" "),s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaUrl")])]),t._v(" "),s("td",[t._v("An array containing a "),s("code",[t._v("url")]),t._v(" key to the file. A temporal or signed url is returned for private files.")])]),t._v(" "),s("tr",[s("td",[s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaUrls")])]),t._v(" "),s("td",[t._v("An array of "),s("code",[t._v("MOIREI\\MediaLibrary\\Casts\\MediaUrl")])])])])]),t._v(" "),s("h2",{attrs:{id:"accessors"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#accessors"}},[t._v("#")]),t._v(" Accessors")]),t._v(" "),s("p",[t._v("You can further defined the following to help access your model's media contents.")]),t._v(" "),s("div",{staticClass:"language-php extra-class"},[s("pre",{pre:!0,attrs:{class:"language-php"}},[s("code",[t._v("\n"),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("getImagesAttribute")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n  "),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("media")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("ofType")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'image'")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("get")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n\n"),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("getAudiosAttribute")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n  "),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("media")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("ofType")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'audio'")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("get")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n\n"),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("getVideosAttribute")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n  "),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("media")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("ofType")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'video'")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("get")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])])}),[],!1,null,null,null);a.default=n.exports}}]);