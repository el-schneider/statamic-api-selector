function m(t,e,n,o,s,r,c,f){var i=typeof t=="function"?t.options:t;e&&(i.render=e,i.staticRenderFns=n,i._compiled=!0),o&&(i.functional=!0),r&&(i._scopeId="data-v-"+r);var l;if(c?(l=function(a){a=a||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!a&&typeof __VUE_SSR_CONTEXT__<"u"&&(a=__VUE_SSR_CONTEXT__),s&&s.call(this,a),a&&a._registeredComponents&&a._registeredComponents.add(c)},i._ssrRegister=l):s&&(l=f?function(){s.call(this,(i.functional?this.parent:this).$root.$options.shadowRoot)}:s),l)if(i.functional){i._injectStyles=l;var d=i.render;i.render=function(h,p){return l.call(p),d(h,p)}}else{var u=i.beforeCreate;i.beforeCreate=u?[].concat(u,l):[l]}return{exports:t,options:i}}const v={mixins:[Fieldtype],data:function(){return{loading:!0,options:this.meta.options}},computed:{selectedOptions(){let t=this.value||[];return(typeof t=="string"||typeof t=="number")&&(t=[t]),t.map(e=>_.findWhere(this.options,{value:e})||{value:e,label:e})},replicatorPreview(){return this.selectedOptions.map(t=>t.label).join(", ")}},methods:{focus(){this.$refs.input.focus()},vueSelectUpdated(t){this.config.multiple?this.update(t.map(e=>e.value)):t?this.update(t.value):this.update(null)},clearCache(){this.$axios.post("/cp/api-selector/clear-cache",{cacheKey:this.meta.cacheKey}).then(t=>{window.location.reload()}).catch(t=>{console.log(t)})}}};var C=function(){var e=this,n=e._self._c;return n("div",[n("v-select",{ref:"input",attrs:{"append-to-body":"",name:e.name,clearable:e.config.clearable,disabled:e.config.disabled||e.isReadOnly,options:e.options,placeholder:e.config.placeholder,searchable:e.config.searchable,multiple:e.config.multiple,"close-on-select":!0,value:e.selectedOptions},on:{input:e.vueSelectUpdated,"search:focus":function(o){return e.$emit("focus")},"search:blur":function(o){return e.$emit("blur")}},scopedSlots:e._u([e.config.multiple?{key:"selected-option-container",fn:function(){return[n("i",{staticClass:"hidden"})]},proxy:!0}:null,{key:"selected-option",fn:function({label:o,thumbnailUrl:s}){return[n("div",{staticClass:"flex items-center"},[s?n("img",{staticClass:"w-5 h-5 mr-2",attrs:{src:s}}):e._e(),e._v(e._s(o)+" ")])]}},e.config.multiple?{key:"search",fn:function({events:o,attributes:s}){return[n("input",e._g(e._b({staticClass:"vs__search",attrs:{placeholder:e.config.placeholder,type:"search"}},"input",s,!1),o))]}}:null,{key:"option",fn:function({thumbnailUrl:o,label:s}){return[n("span",{staticClass:"flex items-center"},[o?n("img",{staticClass:"w-12 h-12",attrs:{src:o}}):e._e(),n("span",{staticClass:"ml-2"},[e._v(e._s(s))])])]}},{key:"no-options",fn:function(){return[n("div",{staticClass:"text-sm text-grey-70 text-left py-1 px-2",domProps:{textContent:e._s(e.__("No options to choose from."))}})]},proxy:!0},e.config.multiple?{key:"footer",fn:function({deselect:o}){return[n("div",{staticClass:"vs__selected-options-outside flex flex-wrap"},e._l(e.selectedOptions,function(s){return n("span",{key:s.value,staticClass:"vs__selected mt-1"},[e._v(" "+e._s(s.label)+" "),n("button",{staticClass:"vs__deselect",attrs:{type:"button","aria-label":e.__("Deselect option")},on:{click:function(r){return o(s)}}},[n("span",[e._v("×")])])])}),0)]}}:null],null,!0)}),n("div",{staticClass:"text-xs text-gray-600 leading-tight flex"},[n("button",{staticClass:"text-blue underline whitespace-nowrap mt-2 hover:text-blue-800 ml-auto",on:{click:e.clearCache}},[e._v(" Clear Cache ")])])],1)},g=[],y=m(v,C,g,!1,null,null,null,null);const b=y.exports;Statamic.$components.register("api_selector-fieldtype",b);
