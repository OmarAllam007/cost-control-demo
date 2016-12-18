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
eval("/* harmony default export */ exports[\"default\"] = {\n    // template: document.getElementById(''),\n    props: ['project'],\n    data: function data(){\n        return {\n            resources: [],\n            code: '',\n            resource:'',\n            resource_type:'',\n        }\n    },\n    methods: {\n        loadResources: function loadResources(){\n            var this$1 = this;\n\n            $.ajax({\n                url: '/api/resources/resources/' + this.project, dataType: 'json'\n            }).success(function (response){\n                this$1.resources = response;\n            }).error(function (response){\n                console.log('error');\n            });\n        }\n    },\n    computed: {\n        filtered_resources: function filtered_resources(){\n            var this$1 = this;\n\n            return this.resources.filter(function (item){\n                if (this$1.code) {\n                 return item.resource_code.toLowerCase().indexOf(this$1.code.toLowerCase()) >=0;\n                }\n                return true;\n            }).filter(function (item){\n                if(this$1.resource){\n                    return item.name.toLowerCase().indexOf(this$1.resource.toLowerCase()) >= 0;\n                }\n                return true;\n            });\n        }\n    },\n    watch: {},\n    ready: function ready(){\n        this.loadResources();\n    }\n\n};//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMC5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9yZXNvdXJjZXMvYXNzZXRzL2pzL3Byb2plY3QvY29tcG9uZW50cy9SZXNvdXJjZXMuanM/MjQ3NiJdLCJzb3VyY2VzQ29udGVudCI6WyJleHBvcnQgZGVmYXVsdHtcbiAgICAvLyB0ZW1wbGF0ZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJycpLFxuICAgIHByb3BzOiBbJ3Byb2plY3QnXSxcbiAgICBkYXRhKCl7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICByZXNvdXJjZXM6IFtdLFxuICAgICAgICAgICAgY29kZTogJycsXG4gICAgICAgICAgICByZXNvdXJjZTonJyxcbiAgICAgICAgICAgIHJlc291cmNlX3R5cGU6JycsXG4gICAgICAgIH1cbiAgICB9LFxuICAgIG1ldGhvZHM6IHtcbiAgICAgICAgbG9hZFJlc291cmNlcygpe1xuICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6ICcvYXBpL3Jlc291cmNlcy9yZXNvdXJjZXMvJyArIHRoaXMucHJvamVjdCwgZGF0YVR5cGU6ICdqc29uJ1xuICAgICAgICAgICAgfSkuc3VjY2VzcyhyZXNwb25zZT0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnJlc291cmNlcyA9IHJlc3BvbnNlO1xuICAgICAgICAgICAgfSkuZXJyb3IocmVzcG9uc2U9PiB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ2Vycm9yJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH0sXG4gICAgY29tcHV0ZWQ6IHtcbiAgICAgICAgZmlsdGVyZWRfcmVzb3VyY2VzKCl7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5yZXNvdXJjZXMuZmlsdGVyKChpdGVtKT0+IHtcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5jb2RlKSB7XG4gICAgICAgICAgICAgICAgIHJldHVybiBpdGVtLnJlc291cmNlX2NvZGUudG9Mb3dlckNhc2UoKS5pbmRleE9mKHRoaXMuY29kZS50b0xvd2VyQ2FzZSgpKSA+PTA7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfSkuZmlsdGVyKChpdGVtKT0+e1xuICAgICAgICAgICAgICAgIGlmKHRoaXMucmVzb3VyY2Upe1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gaXRlbS5uYW1lLnRvTG93ZXJDYXNlKCkuaW5kZXhPZih0aGlzLnJlc291cmNlLnRvTG93ZXJDYXNlKCkpID49IDA7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9LFxuICAgIHdhdGNoOiB7fSxcbiAgICByZWFkeSgpe1xuICAgICAgICB0aGlzLmxvYWRSZXNvdXJjZXMoKTtcbiAgICB9XG5cbn1cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gcmVzb3VyY2VzL2Fzc2V0cy9qcy9wcm9qZWN0L2NvbXBvbmVudHMvUmVzb3VyY2VzLmpzIl0sIm1hcHBpbmdzIjoiQUFBQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTsiLCJzb3VyY2VSb290IjoiIn0=");

/***/ }
/******/ ]);