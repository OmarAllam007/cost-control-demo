/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.l = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// identity function for calling harmory imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };

/******/ 	// define getter function for harmory exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		Object.defineProperty(exports, name, {
/******/ 			configurable: false,
/******/ 			enumerable: true,
/******/ 			get: getter
/******/ 		});
/******/ 	};

/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};

/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
eval("/* harmony default export */ exports[\"default\"] = {\n    props: ['project'],\n\n    template: document.getElementById('BOQTemplate').innerHTML,\n\n    data: function data() {\n        return {\n            boq: {},\n            loading: false,\n            wbs_id: 0,\n            wiping: false,\n            filter: '',\n        };\n    },\n\n    computed: {\n        empty_boq: function empty_boq() {\n            return Object.keys(this.boq).length == 0;\n        }\n    },\n\n    methods: {\n        loadBoq: function loadBoq() {\n            var this$1 = this;\n\n            if (this.wbs_id) {\n                this.loading = true;\n                $.ajax({\n                    url: '/api/wbs/boq/' + this.wbs_id, dataType: 'json',\n                    cache: true\n                }).success(function (response) {\n                    this$1.loading = false;\n                    if ($.isPlainObject(response)) {\n                        this$1.boq = response;\n                    } else {\n                        this$1.boq = {};\n                    }\n                }).error(function () {\n                    this$1.loading = false;\n                    this$1.boq = {};\n                });\n            }\n        },\n\n        filtered_boq: function filtered_boq() {\n            var this$1 = this;\n\n            var boqs = this.boq.filter(function (boq) {\n                if (!this$1.filter || this$1.filter == '') {\n                    return true;\n                }\n                var term = this$1.filter.toLowerCase();\n                return qty.description.toLowerCase().indexOf(term) >= 0 ||\n                    qty.cost_account.toLowerCase().indexOf(term) >= 0;\n            });\n\n\n            return quantities.slice(this.first, this.last);\n        },\n\n        destroy: function destroy (item_id) {\n            var this$1 = this;\n\n            this.loading = true;\n            $.ajax({\n                url: '/boq/' + item_id,\n                data: {_token: document.querySelector('meta[name=csrf-token]').content, _method: 'delete'},\n                method: 'post'\n            }).success(function (response) {\n                if (response.ok) {\n                    this$1.loadBoq();\n                }\n            }).error(function () {\n            });\n        },\n\n        wipeAll: function wipeAll() {\n            var this$1 = this;\n\n            this.wiping = true;\n            $.ajax({\n                url: '/boq/wipe/' + this.project,\n                data: {\n                    _token: $('meta[name=csrf-token]').attr('content'),\n                    _method: 'delete', wipe: true\n                },\n                method: 'post', dataType: 'json'\n            }).success(function (response) {\n                this$1.wiping = false;\n                this$1.$dispatch('request_alert', {\n                    message: response.message,\n                    type: response.ok ? 'info' : 'danger'\n                });\n                if (response.ok) {\n                    this$1.boq = [];\n                    this$1.selected = 0;\n                }\n                $('#WipeBoqModal').modal('hide');\n            }).error(function (response) {\n                this$1.wiping = false;\n                this$1.$dispatch('request_alert', {\n                    message: response.message,\n                    type: 'danger'\n                });\n                $('#WipeBoqModal').modal('hide');\n            });\n        }\n    },\n\n    events: {\n        wbs_changed: function wbs_changed(params) {\n            this.wbs_id = params.selection;\n            this.loadBoq();\n        },\n\n        reload_boq: function reload_boq() {\n            this.loadBoq();\n        }\n    }\n};\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMC5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9yZXNvdXJjZXMvYXNzZXRzL2pzL3Byb2plY3QvY29tcG9uZW50cy9Cb3EuanM/ZTc1NiJdLCJzb3VyY2VzQ29udGVudCI6WyJleHBvcnQgZGVmYXVsdCB7XG4gICAgcHJvcHM6IFsncHJvamVjdCddLFxuXG4gICAgdGVtcGxhdGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdCT1FUZW1wbGF0ZScpLmlubmVySFRNTCxcblxuICAgIGRhdGEoKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICBib3E6IHt9LFxuICAgICAgICAgICAgbG9hZGluZzogZmFsc2UsXG4gICAgICAgICAgICB3YnNfaWQ6IDAsXG4gICAgICAgICAgICB3aXBpbmc6IGZhbHNlLFxuICAgICAgICAgICAgZmlsdGVyOiAnJyxcbiAgICAgICAgfTtcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcbiAgICAgICAgZW1wdHlfYm9xKCkge1xuICAgICAgICAgICAgcmV0dXJuIE9iamVjdC5rZXlzKHRoaXMuYm9xKS5sZW5ndGggPT0gMDtcbiAgICAgICAgfVxuICAgIH0sXG5cbiAgICBtZXRob2RzOiB7XG4gICAgICAgIGxvYWRCb3EoKSB7XG4gICAgICAgICAgICBpZiAodGhpcy53YnNfaWQpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmxvYWRpbmcgPSB0cnVlO1xuICAgICAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogJy9hcGkvd2JzL2JvcS8nICsgdGhpcy53YnNfaWQsIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICAgICAgICAgIGNhY2hlOiB0cnVlXG4gICAgICAgICAgICAgICAgfSkuc3VjY2VzcyhyZXNwb25zZSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubG9hZGluZyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICBpZiAoJC5pc1BsYWluT2JqZWN0KHJlc3BvbnNlKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5ib3EgPSByZXNwb25zZTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuYm9xID0ge307XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KS5lcnJvcigoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubG9hZGluZyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmJvcSA9IHt9O1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIGZpbHRlcmVkX2JvcSgpIHtcbiAgICAgICAgICAgIGNvbnN0IGJvcXMgPSB0aGlzLmJvcS5maWx0ZXIoYm9xID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoIXRoaXMuZmlsdGVyIHx8IHRoaXMuZmlsdGVyID09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBjb25zdCB0ZXJtID0gdGhpcy5maWx0ZXIudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gcXR5LmRlc2NyaXB0aW9uLnRvTG93ZXJDYXNlKCkuaW5kZXhPZih0ZXJtKSA+PSAwIHx8XG4gICAgICAgICAgICAgICAgICAgIHF0eS5jb3N0X2FjY291bnQudG9Mb3dlckNhc2UoKS5pbmRleE9mKHRlcm0pID49IDA7XG4gICAgICAgICAgICB9KTtcblxuXG4gICAgICAgICAgICByZXR1cm4gcXVhbnRpdGllcy5zbGljZSh0aGlzLmZpcnN0LCB0aGlzLmxhc3QpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGRlc3Ryb3kgKGl0ZW1faWQpIHtcbiAgICAgICAgICAgIHRoaXMubG9hZGluZyA9IHRydWU7XG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogJy9ib3EvJyArIGl0ZW1faWQsXG4gICAgICAgICAgICAgICAgZGF0YToge190b2tlbjogZG9jdW1lbnQucXVlcnlTZWxlY3RvcignbWV0YVtuYW1lPWNzcmYtdG9rZW5dJykuY29udGVudCwgX21ldGhvZDogJ2RlbGV0ZSd9LFxuICAgICAgICAgICAgICAgIG1ldGhvZDogJ3Bvc3QnXG4gICAgICAgICAgICB9KS5zdWNjZXNzKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2Uub2spIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5sb2FkQm9xKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSkuZXJyb3IoKCkgPT4ge1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgd2lwZUFsbCgpIHtcbiAgICAgICAgICAgIHRoaXMud2lwaW5nID0gdHJ1ZTtcbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiAnL2JvcS93aXBlLycgKyB0aGlzLnByb2plY3QsXG4gICAgICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgICAgICBfdG9rZW46ICQoJ21ldGFbbmFtZT1jc3JmLXRva2VuXScpLmF0dHIoJ2NvbnRlbnQnKSxcbiAgICAgICAgICAgICAgICAgICAgX21ldGhvZDogJ2RlbGV0ZScsIHdpcGU6IHRydWVcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIG1ldGhvZDogJ3Bvc3QnLCBkYXRhVHlwZTogJ2pzb24nXG4gICAgICAgICAgICB9KS5zdWNjZXNzKChyZXNwb25zZSkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMud2lwaW5nID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgdGhpcy4kZGlzcGF0Y2goJ3JlcXVlc3RfYWxlcnQnLCB7XG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2U6IHJlc3BvbnNlLm1lc3NhZ2UsXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6IHJlc3BvbnNlLm9rID8gJ2luZm8nIDogJ2RhbmdlcidcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2Uub2spIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5ib3EgPSBbXTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZWxlY3RlZCA9IDA7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICQoJyNXaXBlQm9xTW9kYWwnKS5tb2RhbCgnaGlkZScpO1xuICAgICAgICAgICAgfSkuZXJyb3IoKHJlc3BvbnNlKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy53aXBpbmcgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICB0aGlzLiRkaXNwYXRjaCgncmVxdWVzdF9hbGVydCcsIHtcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZTogcmVzcG9uc2UubWVzc2FnZSxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ2RhbmdlcidcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAkKCcjV2lwZUJvcU1vZGFsJykubW9kYWwoJ2hpZGUnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIGV2ZW50czoge1xuICAgICAgICB3YnNfY2hhbmdlZChwYXJhbXMpIHtcbiAgICAgICAgICAgIHRoaXMud2JzX2lkID0gcGFyYW1zLnNlbGVjdGlvbjtcbiAgICAgICAgICAgIHRoaXMubG9hZEJvcSgpO1xuICAgICAgICB9LFxuXG4gICAgICAgIHJlbG9hZF9ib3EoKSB7XG4gICAgICAgICAgICB0aGlzLmxvYWRCb3EoKTtcbiAgICAgICAgfVxuICAgIH1cbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gcmVzb3VyY2VzL2Fzc2V0cy9qcy9wcm9qZWN0L2NvbXBvbmVudHMvQm9xLmpzIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTsiLCJzb3VyY2VSb290IjoiIn0=");

/***/ }
/******/ ]);