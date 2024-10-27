/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/elementor/ImportLayoutModal.js":
/*!********************************************!*\
  !*** ./src/elementor/ImportLayoutModal.js ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_responsive_masonry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-responsive-masonry */ "./node_modules/react-responsive-masonry/es/index.js");
/* harmony import */ var _components_CardItem__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/CardItem */ "./src/elementor/components/CardItem.js");
/* harmony import */ var _ImportLayoutModal_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ImportLayoutModal.scss */ "./src/elementor/ImportLayoutModal.scss");

const {
  useEffect,
  useState,
  useRef
} = wp.element;
const {
  Spinner
} = wp.components;
const {
  apiFetch
} = wp;
const {
  __
} = wp.i18n;



const CHECK_ICON = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  width: "9",
  height: "6",
  viewBox: "0 0 9 6",
  fill: "none",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "M3.18778 6C3.03869 5.99939 2.89575 5.94399 2.78943 5.84561L0.165003 3.3754C0.0593532 3.27596 0 3.14109 0 3.00046C0 2.85983 0.0593532 2.72496 0.165003 2.62552C0.270652 2.52608 0.413943 2.47021 0.563354 2.47021C0.712764 2.47021 0.856055 2.52608 0.961705 2.62552L3.18778 4.72078L8.0383 0.155306C8.09061 0.106068 8.15271 0.0670104 8.22106 0.0403629C8.28941 0.0137154 8.36267 0 8.43665 0C8.51063 0 8.58388 0.0137154 8.65223 0.0403629C8.72058 0.0670104 8.78269 0.106068 8.835 0.155306C8.88731 0.204545 8.92881 0.262999 8.95712 0.327332C8.98543 0.391664 9 0.460616 9 0.530249C9 0.599882 8.98543 0.668834 8.95712 0.733167C8.92881 0.797499 8.88731 0.855954 8.835 0.905192L3.58614 5.84561C3.47982 5.94399 3.33688 5.99939 3.18778 6Z",
  fill: "white"
}));

const ImportLayoutModal = props => {
  const searchRef = useRef();
  const [loading, setLoading] = useState(true);
  const [allData, setAllData] = useState([]); //Layout

  const [layoutCategories, setLayoutCategories] = useState([]);
  const [layoutCards, setLayoutCards] = useState([]);
  const [allLayoutCount, setAllLayoutCount] = useState('');
  const [allSectionCount, setAllSectionCount] = useState('');
  const [catID, setCatID] = useState('all');
  const [searchTerm, setSearchTerm] = useState('');
  const [notFoundMessage, setNotFoundMessage] = useState('');
  const selectCatObj = {
    cat: 'all',
    name: 'Layout',
    count: allLayoutCount
  };
  const [selectedCategory, setSelectedCategory] = useState(selectCatObj); //Section

  const [sectionCategories, setSectionCategories] = useState([]); //Others

  const [category, setCategory] = useState('layout');
  const [cards, setCards] = useState([]);
  const [sidebarCategory, setSidebarCategory] = useState([]);
  const [status, setStatus] = useState('all');
  const [totalCount, setTotalCount] = useState('');

  const fetch_all_layouts = () => {
    let currentUserId = rttpgParams.current_user_id;
    const options = {
      method: "POST",
      url: `${rttpgParams.ajaxurl}?action=rttpg_get_el_layouts&nonce=${rttpgParams.nonce}&user_id=${currentUserId}&status=1`,
      headers: {
        "Content-Type": "application/json"
      }
    };
    apiFetch(options).then(response => {
      if (response.success) {
        setAllData(response.data);
        setLayoutCategories(response.data.layouts.category);
        setSectionCategories(response.data.sections.category);
        setAllLayoutCount(response.data.layouts.all_terms);
        setAllSectionCount(response.data.sections.all_terms);
        setLoading(false);
      } else {
        setLoading(false);
      }
    }).catch(error => {
      console.log(error);
      setLoading(false);
    });
  };

  useEffect(() => {
    fetch_all_layouts();
  }, []);
  useEffect(() => {
    var _allData$layouts, _allData$sections;

    if (layoutCategories && category === 'layout') {
      setSidebarCategory(layoutCategories);
    }

    if (sectionCategories && category === 'section') {
      setSidebarCategory(sectionCategories);
    }

    const allLayouts = allData === null || allData === void 0 ? void 0 : (_allData$layouts = allData.layouts) === null || _allData$layouts === void 0 ? void 0 : _allData$layouts.posts;
    const allSections = allData === null || allData === void 0 ? void 0 : (_allData$sections = allData.sections) === null || _allData$sections === void 0 ? void 0 : _allData$sections.posts;

    if (allLayouts && category === 'layout') {
      setLayoutCards(allLayouts);
      setCards(allLayouts);
    }

    if (allSections && category === 'section') {
      setLayoutCards(allSections);
      setCards(allSections);
    }
  }, [layoutCategories, sectionCategories, category]); //Filter post and category

  const loadCards = () => {
    let newCards = [...cards];

    if (status === 'pro') {
      newCards = newCards.filter(item => item.status !== 'free');
      setTotalCount(newCards.length);
    }

    if (status === 'free') {
      newCards = newCards.filter(item => item.status === 'free');
      setTotalCount(newCards.length);
    }

    if (searchTerm) {
      newCards = newCards.filter(item => item.title.toLowerCase().includes(searchTerm));

      if (newCards.length === 0) {
        setNotFoundMessage("Sorry, we couldn't find the match.");
      } else {
        setNotFoundMessage('');
        setTotalCount(newCards.length);
      }
    } else {
      setNotFoundMessage('');
    }

    if (selectedCategory.cat != 'all') {
      let newLayout = newCards.filter(item => item.category === selectedCategory.cat);
      setLayoutCards(newLayout);
      setTotalCount(newLayout.length);
    } else {
      setLayoutCards(newCards);
    }

    setCatID(selectedCategory.cat);
  };

  const layoutClickHandle = layout => {
    setCategory(layout);
    setCatID('all');
    setSearchTerm('');
    setStatus('all');
    setTotalCount('');
    setTimeout(function () {
      setCatID('all');
      setSelectedCategory({
        cat: 'all',
        name: layout === 'layout' ? 'Layout' : 'Sections',
        count: layout === 'layout' ? allLayoutCount : allSectionCount
      });
    }, 100);
  };

  useEffect(() => {
    setLayoutCards([]);
    loadCards();
  }, ['', status, selectedCategory, searchTerm]);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: `rttpg-layout-modal-inner`
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: `rttpg-modal-inner`
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: `rttpg-modal-inner modal-${category}`
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-layout-modal-header"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-layout-header-inner"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: category === 'layout' ? 'active' : 'button',
    onClick: () => layoutClickHandle('layout')
  }, __("Starter Layouts")), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: category === 'section' ? 'active' : 'button',
    onClick: () => layoutClickHandle('section')
  }, __("Premade Section"))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "layout-search-wrapper"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    ref: searchRef,
    type: "text",
    onChange: e => setSearchTerm(e.target.value),
    value: searchTerm,
    placeholder: __("Search Here..."),
    className: "form-control"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: `tpg-search-empty`,
    onClick: () => {
      setSearchTerm('');
      searchRef.current.focus();
    }
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
    width: "27",
    height: "27",
    viewBox: "0 0 27 27",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M26.77 25.6596L20.6351 19.5248C22.518 17.3777 23.5569 14.6608 23.5569 11.7785C23.5569 8.63218 22.3316 5.67452 20.107 3.44996C17.8824 1.22539 14.9248 0 11.7785 0C8.63218 0 5.67452 1.22539 3.44996 3.44996C1.22539 5.67452 0 8.63218 0 11.7785C0 14.9248 1.22539 17.8824 3.44996 20.107C5.67452 22.3316 8.63218 23.5569 11.7785 23.5569C14.6608 23.5569 17.3777 22.518 19.5248 20.6351L25.6596 26.77C25.813 26.9233 26.0139 27 26.2148 27C26.4157 27 26.6166 26.9233 26.77 26.77C27.0767 26.4632 27.0767 25.9663 26.77 25.6596ZM4.56032 18.9966C2.63252 17.0684 1.57046 14.5049 1.57046 11.7785C1.57046 9.05202 2.63252 6.48851 4.56032 4.56032C6.48851 2.63252 9.05202 1.57046 11.7785 1.57046C14.5049 1.57046 17.0684 2.63252 18.9966 4.56032C20.9244 6.48851 21.9865 9.05202 21.9865 11.7785C21.9865 14.5049 20.9244 17.0684 18.9966 18.9966C17.0684 20.9244 14.5049 21.9865 11.7785 21.9865C9.05202 21.9865 6.48851 20.9244 4.56032 18.9966Z",
    fill: "white"
  }))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: "rttpg-modal-close-wrapper",
    onClick: e => {
      window.tmPromo.destroy();
    }
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
    width: "14",
    height: "14",
    viewBox: "0 0 14 14",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M7 8.49049L1.78327 13.7072C1.58809 13.9024 1.33967 14 1.03802 14C0.736375 14 0.487959 13.9024 0.292775 13.7072C0.0975915 13.512 0 13.2636 0 12.962C0 12.6603 0.0975915 12.4119 0.292775 12.2167L5.50951 7L0.292775 1.78327C0.0975915 1.58809 0 1.33967 0 1.03802C0 0.736375 0.0975915 0.487959 0.292775 0.292775C0.487959 0.0975915 0.736375 0 1.03802 0C1.33967 0 1.58809 0.0975915 1.78327 0.292775L7 5.50951L12.2167 0.292775C12.4119 0.0975915 12.6603 0 12.962 0C13.2636 0 13.512 0.0975915 13.7072 0.292775C13.9024 0.487959 14 0.736375 14 1.03802C14 1.33967 13.9024 1.58809 13.7072 1.78327L8.49049 7L13.7072 12.2167C13.9024 12.4119 14 12.6603 14 12.962C14 13.2636 13.9024 13.512 13.7072 13.7072C13.512 13.9024 13.2636 14 12.962 14C12.6603 14 12.4119 13.9024 12.2167 13.7072L7 8.49049Z",
    fill: "white"
  })))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-layout-modal-sidebar"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-modal-sidebar-content"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, __('Categories')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
    className: "rttpg-template-categories"
  }, category === 'layout' && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: catID === 'all' ? 'block active' : 'block',
    onClick: () => {
      setTotalCount('');
      setStatus('all');
      setSelectedCategory({
        cat: 'all',
        name: 'Layout',
        count: allLayoutCount
      });
    }
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `list-checkbox`
  }, CHECK_ICON), __("All Starter Layouts"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `item-count`
  }, allLayoutCount)), category === 'section' && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: catID === 'all' ? 'block active' : 'block',
    onClick: () => {
      setTotalCount('');
      setStatus('all');
      setSelectedCategory({
        cat: 'all',
        name: 'Section',
        count: allSectionCount
      });
    }
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `list-checkbox`
  }, CHECK_ICON), __("All Premade Sections"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `item-count`
  }, allSectionCount)), sidebarCategory && sidebarCategory.map(item => {
    let is_active = catID === item.term_id ? 'active' : '';
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
      className: `block ${is_active}`,
      key: item.term_id,
      onClick: () => setSelectedCategory({
        cat: item.term_id,
        name: item.name,
        count: item.count
      })
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: `list-checkbox`
    }, CHECK_ICON), __(item.name), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: `item-count`
    }, item.count));
  })))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-modal-content-wrapper"
  }, category != "saved_blocks" && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-template-sub-header"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h4", null, category === 'layout' ? allLayoutCount + " Layouts " : allSectionCount + " Sections ", totalCount && "( " + totalCount + " )"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-template-filter-button-group"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: status === 'all' ? "active" : '',
    onClick: () => {
      setStatus('all');
      setTotalCount('');
    }
  }, __("All")), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: status === 'free' ? "active" : '',
    onClick: () => {
      setStatus('free');
    }
  }, __("Free")), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: status === 'pro' ? "active" : '',
    onClick: () => {
      setStatus('pro');
    }
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: rttpgParams.plugin_url + "/assets/images/crown.svg",
    alt: ""
  }), __("Premium")))), !loading ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    id: "modalContainer",
    className: "rttpg-template-list"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-modal-template-container"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    id: "layouts-blocks-list",
    className: "rttpg-builder-page-templates"
  }, notFoundMessage && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `rttpg-not-fount`
  }, notFoundMessage), layoutCards && category === 'layout' && layoutCards.map(item => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_CardItem__WEBPACK_IMPORTED_MODULE_2__["default"], {
    key: item.id,
    data: item,
    importLayout: props.importLayout,
    insertIndex: props.insertIndex
  })), layoutCards && category === 'section' && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(react_responsive_masonry__WEBPACK_IMPORTED_MODULE_1__.ResponsiveMasonry, {
    columnsCountBreakPoints: {
      300: 1,
      768: 2,
      1200: 3
    }
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(react_responsive_masonry__WEBPACK_IMPORTED_MODULE_1__["default"], {
    gutter: "20px"
  }, layoutCards.map(item => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_CardItem__WEBPACK_IMPORTED_MODULE_2__["default"], {
    key: item.id,
    data: item,
    importLayout: props.importLayout,
    insertIndex: props.insertIndex
  }))))))) : (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "tpg-loader"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Spinner, null)))))));
};

/* harmony default export */ __webpack_exports__["default"] = (ImportLayoutModal);

/***/ }),

/***/ "./src/elementor/components/CardItem.js":
/*!**********************************************!*\
  !*** ./src/elementor/components/CardItem.js ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);

const {
  __
} = wp.i18n;
const {
  Fragment
} = wp.element;
const {
  Spinner
} = wp.components;
const {
  apiFetch
} = wp;

function countLyout(layout_id) {
  let currentUserId = rttpgParams.current_user_id;
  const options = {
    method: "POST",
    url: `${rttpgParams.ajaxurl}?action=rttpg_el_layout_count&nonce=${rttpgParams.nonce}&user_id=${currentUserId}&layout_id=${layout_id}`,
    headers: {
      "Content-Type": "application/json"
    }
  };
  apiFetch(options);
}

const CardItem = _ref => {
  let {
    data,
    importLayout,
    insertIndex
  } = _ref;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-layout-column"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-single-block-item "
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-single-item-inner"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-layout-image-wrap"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    className: "lazy-image",
    alt: data.title,
    src: data.image_url
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "tpg-spinner"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Spinner, null)), data.status !== 'free' ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "rttpg-pro"
  }, __("Pro")) : (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "rttpg-free"
  }, __("Free"))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-tmpl-info"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rttpg-import-button-group"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: "rttpg-button",
    target: "_blank",
    href: data.preview_link
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
    width: "20",
    height: "20",
    viewBox: "0 0 20 20",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M19 0H13C12.7348 0 12.4804 0.105357 12.2929 0.292893C12.1054 0.48043 12 0.734783 12 1C12 1.26522 12.1054 1.51957 12.2929 1.70711C12.4804 1.89464 12.7348 2 13 2H16.586L8.293 10.293C8.19749 10.3852 8.12131 10.4956 8.0689 10.6176C8.01649 10.7396 7.9889 10.8708 7.98775 11.0036C7.9866 11.1364 8.0119 11.2681 8.06218 11.391C8.11246 11.5138 8.18671 11.6255 8.28061 11.7194C8.3745 11.8133 8.48615 11.8875 8.60905 11.9378C8.73194 11.9881 8.86362 12.0134 8.9964 12.0122C9.12918 12.0111 9.2604 11.9835 9.38241 11.9311C9.50441 11.8787 9.61475 11.8025 9.707 11.707L18 3.414V7C18 7.26522 18.1054 7.51957 18.2929 7.70711C18.4804 7.89464 18.7348 8 19 8C19.2652 8 19.5196 7.89464 19.7071 7.70711C19.8946 7.51957 20 7.26522 20 7V1C20 0.734783 19.8946 0.48043 19.7071 0.292893C19.5196 0.105357 19.2652 0 19 0Z",
    fill: "black"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M17 8.588C16.7348 8.588 16.4804 8.69336 16.2929 8.88089C16.1054 9.06843 16 9.32278 16 9.588V16.751C15.9997 17.0822 15.8681 17.3997 15.6339 17.6339C15.3997 17.8681 15.0822 17.9997 14.751 18H3.249C2.91783 17.9997 2.60029 17.8681 2.36612 17.6339C2.13194 17.3997 2.00026 17.0822 2 16.751V5.249C2.00026 4.91783 2.13194 4.60029 2.36612 4.36612C2.60029 4.13194 2.91783 4.00026 3.249 4H9.471C9.73621 4 9.99057 3.89464 10.1781 3.70711C10.3656 3.51957 10.471 3.26522 10.471 3C10.471 2.73478 10.3656 2.48043 10.1781 2.29289C9.99057 2.10536 9.73621 2 9.471 2H3.249C2.38764 2.00106 1.56186 2.3437 0.952779 2.95278C0.343703 3.56186 0.00105851 4.38764 0 5.249V16.749C0.000529143 17.6107 0.34294 18.437 0.952073 19.0465C1.56121 19.656 2.38729 19.9989 3.249 20H14.749C15.6107 19.9995 16.437 19.6571 17.0465 19.0479C17.656 18.4388 17.9989 17.6127 18 16.751V9.588C18 9.32278 17.8946 9.06843 17.7071 8.88089C17.5196 8.69336 17.2652 8.588 17 8.588Z",
    fill: "black"
  })), __("Preview"), " "), data.status !== "free" && !rttpgParams.hasPro ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: "rttpg-button-download",
    target: "_blank",
    href: "//www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
    width: "22",
    height: "20",
    viewBox: "0 0 22 20",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M10.9917 0.522392C11.0869 0.522318 11.1811 0.540613 11.2691 0.576231C11.3571 0.611849 11.437 0.664092 11.5043 0.72997C11.5716 0.795848 11.6249 0.874068 11.6613 0.960155C11.6977 1.04624 11.7164 1.13851 11.7163 1.23167L11.7163 11.8391C11.7163 12.0272 11.64 12.2076 11.5041 12.3407C11.3682 12.4737 11.1839 12.5484 10.9917 12.5484C10.7995 12.5484 10.6152 12.4737 10.4793 12.3407C10.3434 12.2076 10.2671 12.0272 10.2671 11.8391L10.2671 1.23167C10.2671 1.04356 10.3434 0.863153 10.4793 0.730137C10.6152 0.597121 10.7995 0.522392 10.9917 0.522392Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    "fill-rule": "evenodd",
    "clip-rule": "evenodd",
    d: "M10.9916 0.322405C11.1124 0.32231 11.2323 0.345541 11.3442 0.390868C11.4561 0.436199 11.5581 0.502796 11.6442 0.587055C11.7303 0.671322 11.7988 0.77161 11.8456 0.882298C11.8923 0.992971 11.9164 1.11174 11.9163 1.23177C11.9163 1.2318 11.9163 1.23182 11.9163 1.23185L11.7169 1.23169L11.9163 1.23169L11.9163 1.23177L11.9163 11.8391C11.9163 12.0817 11.8179 12.3134 11.644 12.4836C11.4703 12.6536 11.2356 12.7484 10.9917 12.7484C10.7478 12.7484 10.5131 12.6536 10.3394 12.4836C10.1655 12.3134 10.0671 12.0817 10.0671 11.8391L10.0671 1.23169C10.0671 0.989131 10.1655 0.757398 10.3394 0.587221C10.5131 0.417222 10.7477 0.322444 10.9916 0.322405ZM11.2691 0.576243C11.1812 0.540626 11.0869 0.52233 10.9917 0.522405C10.7995 0.522405 10.6152 0.597133 10.4793 0.730149C10.3434 0.863165 10.2671 1.04357 10.2671 1.23169L10.2671 11.8391C10.2671 12.0272 10.3434 12.2077 10.4793 12.3407C10.6152 12.4737 10.7995 12.5484 10.9917 12.5484C11.1839 12.5484 11.3682 12.4737 11.5041 12.3407C11.64 12.2077 11.7163 12.0272 11.7163 11.8391L11.7163 1.23169C11.7164 1.13852 11.6977 1.04626 11.6613 0.960168C11.625 0.87408 11.5716 0.79586 11.5043 0.729982C11.437 0.664104 11.3571 0.611862 11.2691 0.576243Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M10.9917 0.203482C11.2352 0.203103 11.4765 0.249829 11.7015 0.340973C11.9265 0.432118 12.1309 0.565886 12.303 0.734592L16.2166 4.56528C16.3525 4.69824 16.4288 4.87857 16.4288 5.0666C16.4288 5.25463 16.3525 5.43496 16.2166 5.56792C16.0808 5.70088 15.8966 5.77557 15.7045 5.77557C15.5124 5.77557 15.3281 5.70088 15.1923 5.56792L11.2787 1.73723C11.241 1.70034 11.1962 1.67108 11.147 1.65112C11.0978 1.63116 11.045 1.62088 10.9917 1.62088C10.9384 1.62088 10.8856 1.63116 10.8364 1.65112C10.7872 1.67108 10.7424 1.70034 10.7047 1.73723L6.79112 5.56792C6.65449 5.69716 6.47147 5.7687 6.28147 5.76714C6.09147 5.76557 5.9097 5.69103 5.77531 5.55956C5.64092 5.42809 5.56466 5.25022 5.56295 5.06424C5.56125 4.87827 5.63424 4.69909 5.7662 4.56528L9.6804 0.734592C9.8525 0.565936 10.0569 0.432201 10.2819 0.341059C10.507 0.249918 10.7482 0.203165 10.9917 0.203482Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    "fill-rule": "evenodd",
    "clip-rule": "evenodd",
    d: "M10.7047 1.73724L6.79112 5.56793C6.65449 5.69717 6.47147 5.76871 6.28147 5.76715C6.09147 5.76559 5.9097 5.69104 5.77531 5.55957C5.64091 5.4281 5.56465 5.25023 5.56295 5.06426C5.56125 4.87829 5.63423 4.6991 5.76619 4.56529L9.6804 0.734604C9.85249 0.565948 10.0569 0.432213 10.2819 0.341072C10.507 0.24993 10.7482 0.203177 10.9917 0.203494C11.2352 0.203115 11.4765 0.24984 11.7015 0.340985C11.9265 0.43213 12.1309 0.565898 12.303 0.734604L16.2166 4.56529C16.3525 4.69825 16.4288 4.87858 16.4288 5.06661C16.4288 5.25464 16.3525 5.43497 16.2166 5.56793C16.0808 5.70089 15.8966 5.77559 15.7045 5.77559C15.5124 5.77559 15.3281 5.70089 15.1923 5.56793L11.2787 1.73724C11.241 1.70036 11.1962 1.6711 11.147 1.65113C11.0978 1.63117 11.045 1.62089 10.9917 1.62089C10.9384 1.62089 10.8856 1.63117 10.8364 1.65113C10.7872 1.6711 10.7424 1.70036 10.7047 1.73724ZM9.54041 0.591762L5.6263 4.42235L5.62379 4.42486C5.45492 4.59609 5.36076 4.8263 5.36296 5.06609C5.36516 5.30588 5.46351 5.53435 5.63545 5.70254C5.80724 5.8706 6.03873 5.96516 6.27982 5.96714C6.52091 5.96913 6.75395 5.87839 6.92856 5.71323L10.8446 1.88017C10.8636 1.86165 10.8863 1.84673 10.9115 1.83648C10.9368 1.82622 10.9641 1.82089 10.9917 1.82089C11.0193 1.82089 11.0466 1.82622 11.0719 1.83648C11.0971 1.84673 11.1198 1.86164 11.1388 1.88017L15.0524 5.71086C15.226 5.88084 15.4607 5.97559 15.7045 5.97559C15.9482 5.97559 16.1829 5.88084 16.3565 5.71086C16.5303 5.54074 16.6288 5.30909 16.6288 5.06661C16.6288 4.82414 16.5303 4.59248 16.3565 4.42236L12.443 0.591798C12.2522 0.404655 12.0256 0.256491 11.7766 0.155613C11.5276 0.0547734 11.2609 0.00311138 10.9917 0.0034933",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M19.0788 19H2.92116C2.41182 18.9994 1.92353 18.8011 1.56337 18.4486C1.20322 18.0961 1.00061 17.6181 1 17.1196V12.1614C1 11.9733 1.07635 11.7929 1.21224 11.6599C1.34814 11.5269 1.53245 11.4521 1.72464 11.4521C1.91682 11.4521 2.10114 11.5269 2.23703 11.6599C2.37293 11.7929 2.44928 11.9733 2.44928 12.1614V17.1196C2.44943 17.242 2.49919 17.3594 2.58766 17.446C2.67612 17.5326 2.79605 17.5813 2.92116 17.5815H19.0788C19.2039 17.5813 19.3239 17.5326 19.4123 17.446C19.5008 17.3594 19.5506 17.242 19.5507 17.1196V12.1614C19.5507 11.9733 19.6271 11.7929 19.763 11.6599C19.8989 11.5269 20.0832 11.4521 20.2754 11.4521C20.4675 11.4521 20.6519 11.5269 20.7878 11.6599C20.9237 11.7929 21 11.9733 21 12.1614V17.1196C20.9994 17.6181 20.7968 18.0961 20.4366 18.4486C20.0765 18.8011 19.5882 18.9994 19.0788 19Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    "fill-rule": "evenodd",
    "clip-rule": "evenodd",
    d: "M19.0788 19.2H2.92116C2.36023 19.1994 1.82139 18.981 1.42347 18.5915C1.02541 18.2019 0.80068 17.6727 0.799999 17.1198L0.799999 12.1614C0.799999 11.9189 0.89848 11.6871 1.07234 11.517C1.24606 11.3469 1.48077 11.2521 1.72464 11.2521C1.9685 11.2521 2.20322 11.3469 2.37693 11.517C2.55079 11.6871 2.64927 11.9189 2.64927 12.1614L2.64927 17.1193C2.64927 17.1194 2.64927 17.1193 2.64927 17.1193C2.64938 17.1874 2.67702 17.2536 2.72755 17.3031C2.77826 17.3527 2.84789 17.3814 2.9214 17.3815H19.0786C19.1521 17.3814 19.2217 17.3527 19.2724 17.3031C19.323 17.2536 19.3506 17.1874 19.3507 17.1193V12.1614C19.3507 11.9189 19.4492 11.6871 19.6231 11.517C19.7968 11.3469 20.0315 11.2521 20.2754 11.2521C20.5192 11.2521 20.7539 11.3469 20.9277 11.517C21.1015 11.6871 21.2 11.9189 21.2 12.1614V17.1196C21.1993 17.6725 20.9746 18.2019 20.5765 18.5915C20.1786 18.981 19.6398 19.1994 19.0788 19.2ZM19.0788 17.5815H2.92116C2.79605 17.5813 2.67612 17.5326 2.58766 17.446C2.49919 17.3594 2.44943 17.242 2.44927 17.1196V12.1614C2.44927 11.9733 2.37293 11.7929 2.23703 11.6599C2.10114 11.5269 1.91682 11.4521 1.72464 11.4521C1.53245 11.4521 1.34814 11.5269 1.21224 11.6599C1.07634 11.7929 0.999999 11.9733 0.999999 12.1614V17.1196C1.00061 17.6181 1.20322 18.0961 1.56337 18.4486C1.92353 18.8011 2.41182 18.9994 2.92116 19H19.0788C19.5882 18.9994 20.0765 18.8011 20.4366 18.4486C20.7968 18.0961 20.9994 17.6181 21 17.1196V12.1614C21 11.9733 20.9237 11.7929 20.7878 11.6599C20.6519 11.5269 20.4675 11.4521 20.2754 11.4521C20.0832 11.4521 19.8989 11.5269 19.763 11.6599C19.6271 11.7929 19.5507 11.9733 19.5507 12.1614V17.1196C19.5506 17.242 19.5008 17.3594 19.4123 17.446C19.3239 17.5326 19.2039 17.5813 19.0788 17.5815Z",
    fill: "white"
  })), __("Upgrade to Pro")) : (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: "rttpg-button rttpg-button-download",
    onClick: e => {
      importLayout(data.content, insertIndex);
      countLyout(data.id);
      console.log(data.id);
    }
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
    width: "22",
    height: "20",
    viewBox: "0 0 22 20",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M11 12.2295C10.9048 12.2296 10.8106 12.2113 10.7226 12.1757C10.6347 12.14 10.5547 12.0878 10.4874 12.0219C10.4201 11.956 10.3668 11.8778 10.3304 11.7917C10.294 11.7056 10.2753 11.6134 10.2754 11.5202L10.2754 0.912773C10.2754 0.72466 10.3517 0.544251 10.4876 0.411235C10.6235 0.278219 10.8078 0.203491 11 0.203491C11.1922 0.203491 11.3765 0.278219 11.5124 0.411235C11.6483 0.544251 11.7246 0.72466 11.7246 0.912773L11.7246 11.5202C11.7246 11.7083 11.6483 11.8887 11.5124 12.0218C11.3765 12.1548 11.1922 12.2295 11 12.2295Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    "fill-rule": "evenodd",
    "clip-rule": "evenodd",
    d: "M11.0002 12.4295C10.8793 12.4296 10.7595 12.4064 10.6475 12.361C10.5356 12.3157 10.4336 12.2491 10.3475 12.1648C10.2614 12.0806 10.1929 11.9803 10.1461 11.8696C10.0994 11.7589 10.0753 11.6402 10.0754 11.5201C10.0754 11.5201 10.0754 11.5201 10.0754 11.52L10.2748 11.5202H10.0754L10.0754 11.5201L10.0754 0.91276C10.0754 0.670205 10.1738 0.438473 10.3477 0.268295C10.5214 0.0982601 10.7561 0.003479 11 0.003479C11.2439 0.003479 11.4786 0.0982601 11.6523 0.268295C11.8262 0.438473 11.9246 0.670205 11.9246 0.91276L11.9246 11.5202C11.9246 11.7628 11.8262 11.9945 11.6523 12.1647C11.4786 12.3347 11.244 12.4294 11.0002 12.4295ZM10.7226 12.1756C10.8106 12.2113 10.9048 12.2296 11 12.2295C11.1922 12.2295 11.3765 12.1548 11.5124 12.0217C11.6483 11.8887 11.7246 11.7083 11.7246 11.5202L11.7246 0.91276C11.7246 0.724647 11.6483 0.544239 11.5124 0.411223C11.3765 0.278207 11.1922 0.203479 11 0.203479C10.8078 0.203479 10.6235 0.278207 10.4876 0.411223C10.3517 0.544239 10.2754 0.724647 10.2754 0.91276L10.2754 11.5202C10.2753 11.6134 10.294 11.7056 10.3304 11.7917C10.3668 11.8778 10.4201 11.956 10.4874 12.0219C10.5547 12.0878 10.6347 12.14 10.7226 12.1756Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M11 12.5484C10.7565 12.5488 10.5152 12.5021 10.2902 12.4109C10.0652 12.3198 9.86076 12.186 9.6887 12.0173L5.77508 8.18661C5.63924 8.05365 5.56293 7.87332 5.56293 7.68529C5.56293 7.49726 5.63924 7.31693 5.77508 7.18397C5.91091 7.05101 6.09515 6.97632 6.28725 6.97632C6.47935 6.97632 6.66359 7.05101 6.79942 7.18397L10.713 11.0147C10.7507 11.0515 10.7955 11.0808 10.8447 11.1008C10.8939 11.1207 10.9467 11.131 11 11.131C11.0533 11.131 11.1061 11.1207 11.1553 11.1008C11.2045 11.0808 11.2493 11.0515 11.287 11.0147L15.2006 7.18397C15.3372 7.05473 15.5202 6.98319 15.7102 6.98475C15.9002 6.98632 16.082 7.06086 16.2164 7.19233C16.3508 7.3238 16.427 7.50168 16.4288 7.68765C16.4305 7.87362 16.3575 8.0528 16.2255 8.18661L12.3113 12.0173C12.1392 12.186 11.9348 12.3197 11.7098 12.4108C11.4847 12.502 11.2435 12.5487 11 12.5484Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    "fill-rule": "evenodd",
    "clip-rule": "evenodd",
    d: "M11.287 11.0146L15.2006 7.18396C15.3372 7.05472 15.5202 6.98318 15.7102 6.98474C15.9002 6.9863 16.082 7.06085 16.2164 7.19232C16.3508 7.32379 16.4271 7.50166 16.4288 7.68763C16.4305 7.87361 16.3575 8.05279 16.2255 8.1866L12.3113 12.0173C12.1392 12.1859 11.9348 12.3197 11.7098 12.4108C11.4847 12.502 11.2435 12.5487 11 12.5484C10.7565 12.5488 10.5153 12.5021 10.2902 12.4109C10.0652 12.3198 9.86076 12.186 9.6887 12.0173L5.77508 8.1866C5.63924 8.05364 5.56293 7.87331 5.56293 7.68528C5.56293 7.49725 5.63924 7.31692 5.77508 7.18396C5.91092 7.051 6.09515 6.97631 6.28725 6.97631C6.47936 6.97631 6.66359 7.051 6.79943 7.18396L10.713 11.0146C10.7507 11.0515 10.7955 11.0808 10.8447 11.1008C10.8939 11.1207 10.9467 11.131 11 11.131C11.0533 11.131 11.1061 11.1207 11.1553 11.1008C11.2045 11.0808 11.2493 11.0515 11.287 11.0146ZM12.4513 12.1601L16.3654 8.32954L16.3679 8.32703C16.5368 8.1558 16.6309 7.92559 16.6287 7.6858C16.6266 7.44602 16.5282 7.21755 16.3563 7.04935C16.1845 6.88129 15.953 6.78673 15.7119 6.78475C15.4708 6.78276 15.2378 6.8735 15.0631 7.03866L11.1471 10.8717C11.1281 10.8902 11.1054 10.9052 11.0802 10.9154C11.0549 10.9257 11.0276 10.931 11 10.931C10.9724 10.931 10.9451 10.9257 10.9199 10.9154C10.8946 10.9052 10.8719 10.8902 10.853 10.8717L6.93933 7.04103C6.76567 6.87106 6.53103 6.77631 6.28725 6.77631C6.04347 6.77631 5.80884 6.87106 5.63518 7.04103C5.46138 7.21115 5.36293 7.44281 5.36293 7.68528C5.36293 7.92775 5.46138 8.15941 5.63518 8.32953L9.54868 12.1601C9.73954 12.3472 9.96607 12.4954 10.2151 12.5963C10.4641 12.6971 10.7308 12.7488 11 12.7484",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: "M19.0788 19H2.92116C2.41182 18.9994 1.92353 18.8011 1.56337 18.4486C1.20322 18.0961 1.00061 17.6181 1 17.1196V12.1614C1 11.9733 1.07635 11.7929 1.21224 11.6599C1.34814 11.5269 1.53245 11.4521 1.72464 11.4521C1.91682 11.4521 2.10114 11.5269 2.23703 11.6599C2.37293 11.7929 2.44928 11.9733 2.44928 12.1614V17.1196C2.44943 17.242 2.49919 17.3594 2.58766 17.446C2.67612 17.5326 2.79605 17.5813 2.92116 17.5815H19.0788C19.2039 17.5813 19.3239 17.5326 19.4123 17.446C19.5008 17.3594 19.5506 17.242 19.5507 17.1196V12.1614C19.5507 11.9733 19.6271 11.7929 19.763 11.6599C19.8989 11.5269 20.0832 11.4521 20.2754 11.4521C20.4675 11.4521 20.6519 11.5269 20.7878 11.6599C20.9237 11.7929 21 11.9733 21 12.1614V17.1196C20.9994 17.6181 20.7968 18.0961 20.4366 18.4486C20.0765 18.8011 19.5882 18.9994 19.0788 19Z",
    fill: "white"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    "fill-rule": "evenodd",
    "clip-rule": "evenodd",
    d: "M19.0788 19.2H2.92116C2.36023 19.1994 1.82139 18.981 1.42348 18.5915C1.02542 18.2019 0.800684 17.6727 0.800003 17.1198L0.800003 12.1614C0.800003 11.9189 0.898484 11.6871 1.07235 11.517C1.24606 11.3469 1.48078 11.2521 1.72464 11.2521C1.9685 11.2521 2.20322 11.3469 2.37694 11.517C2.5508 11.6871 2.64928 11.9189 2.64928 12.1614L2.64928 17.1193C2.64928 17.1194 2.64928 17.1193 2.64928 17.1193C2.64939 17.1874 2.67702 17.2536 2.72756 17.3031C2.77826 17.3527 2.84789 17.3814 2.9214 17.3815H19.0786C19.1521 17.3814 19.2217 17.3527 19.2724 17.3031C19.323 17.2536 19.3506 17.1874 19.3507 17.1193V12.1614C19.3507 11.9189 19.4492 11.6871 19.6231 11.517C19.7968 11.3469 20.0315 11.2521 20.2754 11.2521C20.5192 11.2521 20.7539 11.3469 20.9277 11.517C21.1015 11.6871 21.2 11.9189 21.2 12.1614V17.1196C21.1993 17.6725 20.9746 18.2019 20.5765 18.5915C20.1786 18.981 19.6398 19.1994 19.0788 19.2ZM19.0788 17.5815H2.92116C2.79606 17.5813 2.67612 17.5326 2.58766 17.446C2.4992 17.3594 2.44943 17.242 2.44928 17.1196V12.1614C2.44928 11.9733 2.37293 11.7929 2.23704 11.6599C2.10114 11.5269 1.91683 11.4521 1.72464 11.4521C1.53245 11.4521 1.34814 11.5269 1.21224 11.6599C1.07635 11.7929 1 11.9733 1 12.1614V17.1196C1.00062 17.6181 1.20322 18.0961 1.56338 18.4486C1.92353 18.8011 2.41183 18.9994 2.92116 19H19.0788C19.5882 18.9994 20.0765 18.8011 20.4366 18.4486C20.7968 18.0961 20.9994 17.6181 21 17.1196V12.1614C21 11.9733 20.9237 11.7929 20.7878 11.6599C20.6519 11.5269 20.4676 11.4521 20.2754 11.4521C20.0832 11.4521 19.8989 11.5269 19.763 11.6599C19.6271 11.7929 19.5507 11.9733 19.5507 12.1614V17.1196C19.5506 17.242 19.5008 17.3594 19.4123 17.446C19.3239 17.5326 19.2039 17.5813 19.0788 17.5815Z",
    fill: "white"
  })), __("Import")))))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h4", {
    className: "rttpg-tmpl-title"
  }, data.title)));
}; // export default CardItem;


/* harmony default export */ __webpack_exports__["default"] = (CardItem);

/***/ }),

/***/ "./src/elementor/ImportLayoutModal.scss":
/*!**********************************************!*\
  !*** ./src/elementor/ImportLayoutModal.scss ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/object-assign/index.js":
/*!*********************************************!*\
  !*** ./node_modules/object-assign/index.js ***!
  \*********************************************/
/***/ (function(module) {

"use strict";
/*
object-assign
(c) Sindre Sorhus
@license MIT
*/


/* eslint-disable no-unused-vars */
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var propIsEnumerable = Object.prototype.propertyIsEnumerable;

function toObject(val) {
	if (val === null || val === undefined) {
		throw new TypeError('Object.assign cannot be called with null or undefined');
	}

	return Object(val);
}

function shouldUseNative() {
	try {
		if (!Object.assign) {
			return false;
		}

		// Detect buggy property enumeration order in older V8 versions.

		// https://bugs.chromium.org/p/v8/issues/detail?id=4118
		var test1 = new String('abc');  // eslint-disable-line no-new-wrappers
		test1[5] = 'de';
		if (Object.getOwnPropertyNames(test1)[0] === '5') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test2 = {};
		for (var i = 0; i < 10; i++) {
			test2['_' + String.fromCharCode(i)] = i;
		}
		var order2 = Object.getOwnPropertyNames(test2).map(function (n) {
			return test2[n];
		});
		if (order2.join('') !== '0123456789') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test3 = {};
		'abcdefghijklmnopqrst'.split('').forEach(function (letter) {
			test3[letter] = letter;
		});
		if (Object.keys(Object.assign({}, test3)).join('') !==
				'abcdefghijklmnopqrst') {
			return false;
		}

		return true;
	} catch (err) {
		// We don't expect any of the above to throw, but better to be safe.
		return false;
	}
}

module.exports = shouldUseNative() ? Object.assign : function (target, source) {
	var from;
	var to = toObject(target);
	var symbols;

	for (var s = 1; s < arguments.length; s++) {
		from = Object(arguments[s]);

		for (var key in from) {
			if (hasOwnProperty.call(from, key)) {
				to[key] = from[key];
			}
		}

		if (getOwnPropertySymbols) {
			symbols = getOwnPropertySymbols(from);
			for (var i = 0; i < symbols.length; i++) {
				if (propIsEnumerable.call(from, symbols[i])) {
					to[symbols[i]] = from[symbols[i]];
				}
			}
		}
	}

	return to;
};


/***/ }),

/***/ "./node_modules/prop-types/checkPropTypes.js":
/*!***************************************************!*\
  !*** ./node_modules/prop-types/checkPropTypes.js ***!
  \***************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var printWarning = function() {};

if (true) {
  var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "./node_modules/prop-types/lib/ReactPropTypesSecret.js");
  var loggedTypeFailures = {};
  var has = __webpack_require__(/*! ./lib/has */ "./node_modules/prop-types/lib/has.js");

  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) { /**/ }
  };
}

/**
 * Assert that the values match with the type specs.
 * Error messages are memorized and will only be shown once.
 *
 * @param {object} typeSpecs Map of name to a ReactPropType
 * @param {object} values Runtime values that need to be type-checked
 * @param {string} location e.g. "prop", "context", "child context"
 * @param {string} componentName Name of the component for error messages.
 * @param {?Function} getStack Returns the component stack.
 * @private
 */
function checkPropTypes(typeSpecs, values, location, componentName, getStack) {
  if (true) {
    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error;
        // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.
        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            var err = Error(
              (componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' +
              'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' +
              'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.'
            );
            err.name = 'Invariant Violation';
            throw err;
          }
          error = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, ReactPropTypesSecret);
        } catch (ex) {
          error = ex;
        }
        if (error && !(error instanceof Error)) {
          printWarning(
            (componentName || 'React class') + ': type specification of ' +
            location + ' `' + typeSpecName + '` is invalid; the type checker ' +
            'function must return `null` or an `Error` but returned a ' + typeof error + '. ' +
            'You may have forgotten to pass an argument to the type checker ' +
            'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' +
            'shape all require an argument).'
          );
        }
        if (error instanceof Error && !(error.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error.message] = true;

          var stack = getStack ? getStack() : '';

          printWarning(
            'Failed ' + location + ' type: ' + error.message + (stack != null ? stack : '')
          );
        }
      }
    }
  }
}

/**
 * Resets warning cache when testing.
 *
 * @private
 */
checkPropTypes.resetWarningCache = function() {
  if (true) {
    loggedTypeFailures = {};
  }
}

module.exports = checkPropTypes;


/***/ }),

/***/ "./node_modules/prop-types/factoryWithTypeCheckers.js":
/*!************************************************************!*\
  !*** ./node_modules/prop-types/factoryWithTypeCheckers.js ***!
  \************************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactIs = __webpack_require__(/*! react-is */ "./node_modules/prop-types/node_modules/react-is/index.js");
var assign = __webpack_require__(/*! object-assign */ "./node_modules/object-assign/index.js");

var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "./node_modules/prop-types/lib/ReactPropTypesSecret.js");
var has = __webpack_require__(/*! ./lib/has */ "./node_modules/prop-types/lib/has.js");
var checkPropTypes = __webpack_require__(/*! ./checkPropTypes */ "./node_modules/prop-types/checkPropTypes.js");

var printWarning = function() {};

if (true) {
  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  };
}

function emptyFunctionThatReturnsNull() {
  return null;
}

module.exports = function(isValidElement, throwOnDirectAccess) {
  /* global Symbol */
  var ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
  var FAUX_ITERATOR_SYMBOL = '@@iterator'; // Before Symbol spec.

  /**
   * Returns the iterator method function contained on the iterable object.
   *
   * Be sure to invoke the function with the iterable as context:
   *
   *     var iteratorFn = getIteratorFn(myIterable);
   *     if (iteratorFn) {
   *       var iterator = iteratorFn.call(myIterable);
   *       ...
   *     }
   *
   * @param {?object} maybeIterable
   * @return {?function}
   */
  function getIteratorFn(maybeIterable) {
    var iteratorFn = maybeIterable && (ITERATOR_SYMBOL && maybeIterable[ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL]);
    if (typeof iteratorFn === 'function') {
      return iteratorFn;
    }
  }

  /**
   * Collection of methods that allow declaration and validation of props that are
   * supplied to React components. Example usage:
   *
   *   var Props = require('ReactPropTypes');
   *   var MyArticle = React.createClass({
   *     propTypes: {
   *       // An optional string prop named "description".
   *       description: Props.string,
   *
   *       // A required enum prop named "category".
   *       category: Props.oneOf(['News','Photos']).isRequired,
   *
   *       // A prop named "dialog" that requires an instance of Dialog.
   *       dialog: Props.instanceOf(Dialog).isRequired
   *     },
   *     render: function() { ... }
   *   });
   *
   * A more formal specification of how these methods are used:
   *
   *   type := array|bool|func|object|number|string|oneOf([...])|instanceOf(...)
   *   decl := ReactPropTypes.{type}(.isRequired)?
   *
   * Each and every declaration produces a function with the same signature. This
   * allows the creation of custom validation functions. For example:
   *
   *  var MyLink = React.createClass({
   *    propTypes: {
   *      // An optional string or URI prop named "href".
   *      href: function(props, propName, componentName) {
   *        var propValue = props[propName];
   *        if (propValue != null && typeof propValue !== 'string' &&
   *            !(propValue instanceof URI)) {
   *          return new Error(
   *            'Expected a string or an URI for ' + propName + ' in ' +
   *            componentName
   *          );
   *        }
   *      }
   *    },
   *    render: function() {...}
   *  });
   *
   * @internal
   */

  var ANONYMOUS = '<<anonymous>>';

  // Important!
  // Keep this list in sync with production version in `./factoryWithThrowingShims.js`.
  var ReactPropTypes = {
    array: createPrimitiveTypeChecker('array'),
    bigint: createPrimitiveTypeChecker('bigint'),
    bool: createPrimitiveTypeChecker('boolean'),
    func: createPrimitiveTypeChecker('function'),
    number: createPrimitiveTypeChecker('number'),
    object: createPrimitiveTypeChecker('object'),
    string: createPrimitiveTypeChecker('string'),
    symbol: createPrimitiveTypeChecker('symbol'),

    any: createAnyTypeChecker(),
    arrayOf: createArrayOfTypeChecker,
    element: createElementTypeChecker(),
    elementType: createElementTypeTypeChecker(),
    instanceOf: createInstanceTypeChecker,
    node: createNodeChecker(),
    objectOf: createObjectOfTypeChecker,
    oneOf: createEnumTypeChecker,
    oneOfType: createUnionTypeChecker,
    shape: createShapeTypeChecker,
    exact: createStrictShapeTypeChecker,
  };

  /**
   * inlined Object.is polyfill to avoid requiring consumers ship their own
   * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
   */
  /*eslint-disable no-self-compare*/
  function is(x, y) {
    // SameValue algorithm
    if (x === y) {
      // Steps 1-5, 7-10
      // Steps 6.b-6.e: +0 != -0
      return x !== 0 || 1 / x === 1 / y;
    } else {
      // Step 6.a: NaN == NaN
      return x !== x && y !== y;
    }
  }
  /*eslint-enable no-self-compare*/

  /**
   * We use an Error-like object for backward compatibility as people may call
   * PropTypes directly and inspect their output. However, we don't use real
   * Errors anymore. We don't inspect their stack anyway, and creating them
   * is prohibitively expensive if they are created too often, such as what
   * happens in oneOfType() for any type before the one that matched.
   */
  function PropTypeError(message, data) {
    this.message = message;
    this.data = data && typeof data === 'object' ? data: {};
    this.stack = '';
  }
  // Make `instanceof Error` still work for returned errors.
  PropTypeError.prototype = Error.prototype;

  function createChainableTypeChecker(validate) {
    if (true) {
      var manualPropTypeCallCache = {};
      var manualPropTypeWarningCount = 0;
    }
    function checkType(isRequired, props, propName, componentName, location, propFullName, secret) {
      componentName = componentName || ANONYMOUS;
      propFullName = propFullName || propName;

      if (secret !== ReactPropTypesSecret) {
        if (throwOnDirectAccess) {
          // New behavior only for users of `prop-types` package
          var err = new Error(
            'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
            'Use `PropTypes.checkPropTypes()` to call them. ' +
            'Read more at http://fb.me/use-check-prop-types'
          );
          err.name = 'Invariant Violation';
          throw err;
        } else if ( true && typeof console !== 'undefined') {
          // Old behavior for people using React.PropTypes
          var cacheKey = componentName + ':' + propName;
          if (
            !manualPropTypeCallCache[cacheKey] &&
            // Avoid spamming the console because they are often not actionable except for lib authors
            manualPropTypeWarningCount < 3
          ) {
            printWarning(
              'You are manually calling a React.PropTypes validation ' +
              'function for the `' + propFullName + '` prop on `' + componentName + '`. This is deprecated ' +
              'and will throw in the standalone `prop-types` package. ' +
              'You may be seeing this warning due to a third-party PropTypes ' +
              'library. See https://fb.me/react-warning-dont-call-proptypes ' + 'for details.'
            );
            manualPropTypeCallCache[cacheKey] = true;
            manualPropTypeWarningCount++;
          }
        }
      }
      if (props[propName] == null) {
        if (isRequired) {
          if (props[propName] === null) {
            return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required ' + ('in `' + componentName + '`, but its value is `null`.'));
          }
          return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required in ' + ('`' + componentName + '`, but its value is `undefined`.'));
        }
        return null;
      } else {
        return validate(props, propName, componentName, location, propFullName);
      }
    }

    var chainedCheckType = checkType.bind(null, false);
    chainedCheckType.isRequired = checkType.bind(null, true);

    return chainedCheckType;
  }

  function createPrimitiveTypeChecker(expectedType) {
    function validate(props, propName, componentName, location, propFullName, secret) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== expectedType) {
        // `propValue` being instance of, say, date/regexp, pass the 'object'
        // check, but we can offer a more precise error message here rather than
        // 'of type `object`'.
        var preciseType = getPreciseType(propValue);

        return new PropTypeError(
          'Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + preciseType + '` supplied to `' + componentName + '`, expected ') + ('`' + expectedType + '`.'),
          {expectedType: expectedType}
        );
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createAnyTypeChecker() {
    return createChainableTypeChecker(emptyFunctionThatReturnsNull);
  }

  function createArrayOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside arrayOf.');
      }
      var propValue = props[propName];
      if (!Array.isArray(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an array.'));
      }
      for (var i = 0; i < propValue.length; i++) {
        var error = typeChecker(propValue, i, componentName, location, propFullName + '[' + i + ']', ReactPropTypesSecret);
        if (error instanceof Error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!isValidElement(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!ReactIs.isValidElementType(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement type.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createInstanceTypeChecker(expectedClass) {
    function validate(props, propName, componentName, location, propFullName) {
      if (!(props[propName] instanceof expectedClass)) {
        var expectedClassName = expectedClass.name || ANONYMOUS;
        var actualClassName = getClassName(props[propName]);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + actualClassName + '` supplied to `' + componentName + '`, expected ') + ('instance of `' + expectedClassName + '`.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createEnumTypeChecker(expectedValues) {
    if (!Array.isArray(expectedValues)) {
      if (true) {
        if (arguments.length > 1) {
          printWarning(
            'Invalid arguments supplied to oneOf, expected an array, got ' + arguments.length + ' arguments. ' +
            'A common mistake is to write oneOf(x, y, z) instead of oneOf([x, y, z]).'
          );
        } else {
          printWarning('Invalid argument supplied to oneOf, expected an array.');
        }
      }
      return emptyFunctionThatReturnsNull;
    }

    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      for (var i = 0; i < expectedValues.length; i++) {
        if (is(propValue, expectedValues[i])) {
          return null;
        }
      }

      var valuesString = JSON.stringify(expectedValues, function replacer(key, value) {
        var type = getPreciseType(value);
        if (type === 'symbol') {
          return String(value);
        }
        return value;
      });
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of value `' + String(propValue) + '` ' + ('supplied to `' + componentName + '`, expected one of ' + valuesString + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createObjectOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside objectOf.');
      }
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an object.'));
      }
      for (var key in propValue) {
        if (has(propValue, key)) {
          var error = typeChecker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
          if (error instanceof Error) {
            return error;
          }
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createUnionTypeChecker(arrayOfTypeCheckers) {
    if (!Array.isArray(arrayOfTypeCheckers)) {
       true ? printWarning('Invalid argument supplied to oneOfType, expected an instance of array.') : 0;
      return emptyFunctionThatReturnsNull;
    }

    for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
      var checker = arrayOfTypeCheckers[i];
      if (typeof checker !== 'function') {
        printWarning(
          'Invalid argument supplied to oneOfType. Expected an array of check functions, but ' +
          'received ' + getPostfixForTypeWarning(checker) + ' at index ' + i + '.'
        );
        return emptyFunctionThatReturnsNull;
      }
    }

    function validate(props, propName, componentName, location, propFullName) {
      var expectedTypes = [];
      for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
        var checker = arrayOfTypeCheckers[i];
        var checkerResult = checker(props, propName, componentName, location, propFullName, ReactPropTypesSecret);
        if (checkerResult == null) {
          return null;
        }
        if (checkerResult.data && has(checkerResult.data, 'expectedType')) {
          expectedTypes.push(checkerResult.data.expectedType);
        }
      }
      var expectedTypesMessage = (expectedTypes.length > 0) ? ', expected one of type [' + expectedTypes.join(', ') + ']': '';
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`' + expectedTypesMessage + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createNodeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      if (!isNode(props[propName])) {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`, expected a ReactNode.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function invalidValidatorError(componentName, location, propFullName, key, type) {
    return new PropTypeError(
      (componentName || 'React class') + ': ' + location + ' type `' + propFullName + '.' + key + '` is invalid; ' +
      'it must be a function, usually from the `prop-types` package, but received `' + type + '`.'
    );
  }

  function createShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      for (var key in shapeTypes) {
        var checker = shapeTypes[key];
        if (typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createStrictShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      // We need to check all keys in case some are required but missing from props.
      var allKeys = assign({}, props[propName], shapeTypes);
      for (var key in allKeys) {
        var checker = shapeTypes[key];
        if (has(shapeTypes, key) && typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        if (!checker) {
          return new PropTypeError(
            'Invalid ' + location + ' `' + propFullName + '` key `' + key + '` supplied to `' + componentName + '`.' +
            '\nBad object: ' + JSON.stringify(props[propName], null, '  ') +
            '\nValid keys: ' + JSON.stringify(Object.keys(shapeTypes), null, '  ')
          );
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }

    return createChainableTypeChecker(validate);
  }

  function isNode(propValue) {
    switch (typeof propValue) {
      case 'number':
      case 'string':
      case 'undefined':
        return true;
      case 'boolean':
        return !propValue;
      case 'object':
        if (Array.isArray(propValue)) {
          return propValue.every(isNode);
        }
        if (propValue === null || isValidElement(propValue)) {
          return true;
        }

        var iteratorFn = getIteratorFn(propValue);
        if (iteratorFn) {
          var iterator = iteratorFn.call(propValue);
          var step;
          if (iteratorFn !== propValue.entries) {
            while (!(step = iterator.next()).done) {
              if (!isNode(step.value)) {
                return false;
              }
            }
          } else {
            // Iterator will provide entry [k,v] tuples rather than values.
            while (!(step = iterator.next()).done) {
              var entry = step.value;
              if (entry) {
                if (!isNode(entry[1])) {
                  return false;
                }
              }
            }
          }
        } else {
          return false;
        }

        return true;
      default:
        return false;
    }
  }

  function isSymbol(propType, propValue) {
    // Native Symbol.
    if (propType === 'symbol') {
      return true;
    }

    // falsy value can't be a Symbol
    if (!propValue) {
      return false;
    }

    // 19.4.3.5 Symbol.prototype[@@toStringTag] === 'Symbol'
    if (propValue['@@toStringTag'] === 'Symbol') {
      return true;
    }

    // Fallback for non-spec compliant Symbols which are polyfilled.
    if (typeof Symbol === 'function' && propValue instanceof Symbol) {
      return true;
    }

    return false;
  }

  // Equivalent of `typeof` but with special handling for array and regexp.
  function getPropType(propValue) {
    var propType = typeof propValue;
    if (Array.isArray(propValue)) {
      return 'array';
    }
    if (propValue instanceof RegExp) {
      // Old webkits (at least until Android 4.0) return 'function' rather than
      // 'object' for typeof a RegExp. We'll normalize this here so that /bla/
      // passes PropTypes.object.
      return 'object';
    }
    if (isSymbol(propType, propValue)) {
      return 'symbol';
    }
    return propType;
  }

  // This handles more types than `getPropType`. Only used for error messages.
  // See `createPrimitiveTypeChecker`.
  function getPreciseType(propValue) {
    if (typeof propValue === 'undefined' || propValue === null) {
      return '' + propValue;
    }
    var propType = getPropType(propValue);
    if (propType === 'object') {
      if (propValue instanceof Date) {
        return 'date';
      } else if (propValue instanceof RegExp) {
        return 'regexp';
      }
    }
    return propType;
  }

  // Returns a string that is postfixed to a warning about an invalid type.
  // For example, "undefined" or "of type array"
  function getPostfixForTypeWarning(value) {
    var type = getPreciseType(value);
    switch (type) {
      case 'array':
      case 'object':
        return 'an ' + type;
      case 'boolean':
      case 'date':
      case 'regexp':
        return 'a ' + type;
      default:
        return type;
    }
  }

  // Returns class name of the object, if any.
  function getClassName(propValue) {
    if (!propValue.constructor || !propValue.constructor.name) {
      return ANONYMOUS;
    }
    return propValue.constructor.name;
  }

  ReactPropTypes.checkPropTypes = checkPropTypes;
  ReactPropTypes.resetWarningCache = checkPropTypes.resetWarningCache;
  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ "./node_modules/prop-types/index.js":
/*!******************************************!*\
  !*** ./node_modules/prop-types/index.js ***!
  \******************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (true) {
  var ReactIs = __webpack_require__(/*! react-is */ "./node_modules/prop-types/node_modules/react-is/index.js");

  // By explicitly using `prop-types` you are opting into new development behavior.
  // http://fb.me/prop-types-in-prod
  var throwOnDirectAccess = true;
  module.exports = __webpack_require__(/*! ./factoryWithTypeCheckers */ "./node_modules/prop-types/factoryWithTypeCheckers.js")(ReactIs.isElement, throwOnDirectAccess);
} else {}


/***/ }),

/***/ "./node_modules/prop-types/lib/ReactPropTypesSecret.js":
/*!*************************************************************!*\
  !*** ./node_modules/prop-types/lib/ReactPropTypesSecret.js ***!
  \*************************************************************/
/***/ (function(module) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ "./node_modules/prop-types/lib/has.js":
/*!********************************************!*\
  !*** ./node_modules/prop-types/lib/has.js ***!
  \********************************************/
/***/ (function(module) {

module.exports = Function.call.bind(Object.prototype.hasOwnProperty);


/***/ }),

/***/ "./node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, exports) {

"use strict";
/** @license React v16.13.1
 * react-is.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */





if (true) {
  (function() {
'use strict';

// The Symbol used to tag the ReactElement-like types. If there is no native Symbol
// nor polyfill, then a plain number is used for performance.
var hasSymbol = typeof Symbol === 'function' && Symbol.for;
var REACT_ELEMENT_TYPE = hasSymbol ? Symbol.for('react.element') : 0xeac7;
var REACT_PORTAL_TYPE = hasSymbol ? Symbol.for('react.portal') : 0xeaca;
var REACT_FRAGMENT_TYPE = hasSymbol ? Symbol.for('react.fragment') : 0xeacb;
var REACT_STRICT_MODE_TYPE = hasSymbol ? Symbol.for('react.strict_mode') : 0xeacc;
var REACT_PROFILER_TYPE = hasSymbol ? Symbol.for('react.profiler') : 0xead2;
var REACT_PROVIDER_TYPE = hasSymbol ? Symbol.for('react.provider') : 0xeacd;
var REACT_CONTEXT_TYPE = hasSymbol ? Symbol.for('react.context') : 0xeace; // TODO: We don't use AsyncMode or ConcurrentMode anymore. They were temporary
// (unstable) APIs that have been removed. Can we remove the symbols?

var REACT_ASYNC_MODE_TYPE = hasSymbol ? Symbol.for('react.async_mode') : 0xeacf;
var REACT_CONCURRENT_MODE_TYPE = hasSymbol ? Symbol.for('react.concurrent_mode') : 0xeacf;
var REACT_FORWARD_REF_TYPE = hasSymbol ? Symbol.for('react.forward_ref') : 0xead0;
var REACT_SUSPENSE_TYPE = hasSymbol ? Symbol.for('react.suspense') : 0xead1;
var REACT_SUSPENSE_LIST_TYPE = hasSymbol ? Symbol.for('react.suspense_list') : 0xead8;
var REACT_MEMO_TYPE = hasSymbol ? Symbol.for('react.memo') : 0xead3;
var REACT_LAZY_TYPE = hasSymbol ? Symbol.for('react.lazy') : 0xead4;
var REACT_BLOCK_TYPE = hasSymbol ? Symbol.for('react.block') : 0xead9;
var REACT_FUNDAMENTAL_TYPE = hasSymbol ? Symbol.for('react.fundamental') : 0xead5;
var REACT_RESPONDER_TYPE = hasSymbol ? Symbol.for('react.responder') : 0xead6;
var REACT_SCOPE_TYPE = hasSymbol ? Symbol.for('react.scope') : 0xead7;

function isValidElementType(type) {
  return typeof type === 'string' || typeof type === 'function' || // Note: its typeof might be other than 'symbol' or 'number' if it's a polyfill.
  type === REACT_FRAGMENT_TYPE || type === REACT_CONCURRENT_MODE_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || typeof type === 'object' && type !== null && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || type.$$typeof === REACT_FUNDAMENTAL_TYPE || type.$$typeof === REACT_RESPONDER_TYPE || type.$$typeof === REACT_SCOPE_TYPE || type.$$typeof === REACT_BLOCK_TYPE);
}

function typeOf(object) {
  if (typeof object === 'object' && object !== null) {
    var $$typeof = object.$$typeof;

    switch ($$typeof) {
      case REACT_ELEMENT_TYPE:
        var type = object.type;

        switch (type) {
          case REACT_ASYNC_MODE_TYPE:
          case REACT_CONCURRENT_MODE_TYPE:
          case REACT_FRAGMENT_TYPE:
          case REACT_PROFILER_TYPE:
          case REACT_STRICT_MODE_TYPE:
          case REACT_SUSPENSE_TYPE:
            return type;

          default:
            var $$typeofType = type && type.$$typeof;

            switch ($$typeofType) {
              case REACT_CONTEXT_TYPE:
              case REACT_FORWARD_REF_TYPE:
              case REACT_LAZY_TYPE:
              case REACT_MEMO_TYPE:
              case REACT_PROVIDER_TYPE:
                return $$typeofType;

              default:
                return $$typeof;
            }

        }

      case REACT_PORTAL_TYPE:
        return $$typeof;
    }
  }

  return undefined;
} // AsyncMode is deprecated along with isAsyncMode

var AsyncMode = REACT_ASYNC_MODE_TYPE;
var ConcurrentMode = REACT_CONCURRENT_MODE_TYPE;
var ContextConsumer = REACT_CONTEXT_TYPE;
var ContextProvider = REACT_PROVIDER_TYPE;
var Element = REACT_ELEMENT_TYPE;
var ForwardRef = REACT_FORWARD_REF_TYPE;
var Fragment = REACT_FRAGMENT_TYPE;
var Lazy = REACT_LAZY_TYPE;
var Memo = REACT_MEMO_TYPE;
var Portal = REACT_PORTAL_TYPE;
var Profiler = REACT_PROFILER_TYPE;
var StrictMode = REACT_STRICT_MODE_TYPE;
var Suspense = REACT_SUSPENSE_TYPE;
var hasWarnedAboutDeprecatedIsAsyncMode = false; // AsyncMode should be deprecated

function isAsyncMode(object) {
  {
    if (!hasWarnedAboutDeprecatedIsAsyncMode) {
      hasWarnedAboutDeprecatedIsAsyncMode = true; // Using console['warn'] to evade Babel and ESLint

      console['warn']('The ReactIs.isAsyncMode() alias has been deprecated, ' + 'and will be removed in React 17+. Update your code to use ' + 'ReactIs.isConcurrentMode() instead. It has the exact same API.');
    }
  }

  return isConcurrentMode(object) || typeOf(object) === REACT_ASYNC_MODE_TYPE;
}
function isConcurrentMode(object) {
  return typeOf(object) === REACT_CONCURRENT_MODE_TYPE;
}
function isContextConsumer(object) {
  return typeOf(object) === REACT_CONTEXT_TYPE;
}
function isContextProvider(object) {
  return typeOf(object) === REACT_PROVIDER_TYPE;
}
function isElement(object) {
  return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
}
function isForwardRef(object) {
  return typeOf(object) === REACT_FORWARD_REF_TYPE;
}
function isFragment(object) {
  return typeOf(object) === REACT_FRAGMENT_TYPE;
}
function isLazy(object) {
  return typeOf(object) === REACT_LAZY_TYPE;
}
function isMemo(object) {
  return typeOf(object) === REACT_MEMO_TYPE;
}
function isPortal(object) {
  return typeOf(object) === REACT_PORTAL_TYPE;
}
function isProfiler(object) {
  return typeOf(object) === REACT_PROFILER_TYPE;
}
function isStrictMode(object) {
  return typeOf(object) === REACT_STRICT_MODE_TYPE;
}
function isSuspense(object) {
  return typeOf(object) === REACT_SUSPENSE_TYPE;
}

exports.AsyncMode = AsyncMode;
exports.ConcurrentMode = ConcurrentMode;
exports.ContextConsumer = ContextConsumer;
exports.ContextProvider = ContextProvider;
exports.Element = Element;
exports.ForwardRef = ForwardRef;
exports.Fragment = Fragment;
exports.Lazy = Lazy;
exports.Memo = Memo;
exports.Portal = Portal;
exports.Profiler = Profiler;
exports.StrictMode = StrictMode;
exports.Suspense = Suspense;
exports.isAsyncMode = isAsyncMode;
exports.isConcurrentMode = isConcurrentMode;
exports.isContextConsumer = isContextConsumer;
exports.isContextProvider = isContextProvider;
exports.isElement = isElement;
exports.isForwardRef = isForwardRef;
exports.isFragment = isFragment;
exports.isLazy = isLazy;
exports.isMemo = isMemo;
exports.isPortal = isPortal;
exports.isProfiler = isProfiler;
exports.isStrictMode = isStrictMode;
exports.isSuspense = isSuspense;
exports.isValidElementType = isValidElementType;
exports.typeOf = typeOf;
  })();
}


/***/ }),

/***/ "./node_modules/prop-types/node_modules/react-is/index.js":
/*!****************************************************************!*\
  !*** ./node_modules/prop-types/node_modules/react-is/index.js ***!
  \****************************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


if (false) {} else {
  module.exports = __webpack_require__(/*! ./cjs/react-is.development.js */ "./node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js");
}


/***/ }),

/***/ "./node_modules/react-responsive-masonry/es/Masonry/index.js":
/*!*******************************************************************!*\
  !*** ./node_modules/react-responsive-masonry/es/Masonry/index.js ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! prop-types */ "./node_modules/prop-types/index.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }




var Masonry = /*#__PURE__*/function (_React$Component) {
  _inheritsLoose(Masonry, _React$Component);

  function Masonry() {
    return _React$Component.apply(this, arguments) || this;
  }

  var _proto = Masonry.prototype;

  _proto.getColumns = function getColumns() {
    var _this$props = this.props,
        children = _this$props.children,
        columnsCount = _this$props.columnsCount;
    var columns = Array.from({
      length: columnsCount
    }, function () {
      return [];
    });
    react__WEBPACK_IMPORTED_MODULE_0___default().Children.forEach(children, function (child, index) {
      if (child && react__WEBPACK_IMPORTED_MODULE_0___default().isValidElement(child)) {
        columns[index % columnsCount].push(child);
      }
    });
    return columns;
  };

  _proto.renderColumns = function renderColumns() {
    var gutter = this.props.gutter;
    return this.getColumns().map(function (column, i) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
        key: i,
        style: {
          display: "flex",
          flexDirection: "column",
          justifyContent: "flex-start",
          alignContent: "stretch",
          flex: 1,
          width: 0,
          gap: gutter
        }
      }, column.map(function (item) {
        return item;
      }));
    });
  };

  _proto.render = function render() {
    var _this$props2 = this.props,
        gutter = _this$props2.gutter,
        className = _this$props2.className,
        style = _this$props2.style;
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
      style: _extends({
        display: "flex",
        flexDirection: "row",
        justifyContent: "center",
        alignContent: "stretch",
        boxSizing: "border-box",
        width: "100%",
        gap: gutter
      }, style),
      className: className
    }, this.renderColumns());
  };

  return Masonry;
}((react__WEBPACK_IMPORTED_MODULE_0___default().Component));

Masonry.propTypes =  true ? {
  children: prop_types__WEBPACK_IMPORTED_MODULE_1___default().oneOfType([prop_types__WEBPACK_IMPORTED_MODULE_1___default().arrayOf((prop_types__WEBPACK_IMPORTED_MODULE_1___default().node)), (prop_types__WEBPACK_IMPORTED_MODULE_1___default().node)]).isRequired,
  columnsCount: (prop_types__WEBPACK_IMPORTED_MODULE_1___default().number),
  gutter: (prop_types__WEBPACK_IMPORTED_MODULE_1___default().string),
  className: (prop_types__WEBPACK_IMPORTED_MODULE_1___default().string),
  style: (prop_types__WEBPACK_IMPORTED_MODULE_1___default().object)
} : 0;
Masonry.defaultProps = {
  columnsCount: 3,
  gutter: "0",
  className: null,
  style: {}
};
/* harmony default export */ __webpack_exports__["default"] = (Masonry);

/***/ }),

/***/ "./node_modules/react-responsive-masonry/es/ResponsiveMasonry/index.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/react-responsive-masonry/es/ResponsiveMasonry/index.js ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! prop-types */ "./node_modules/prop-types/index.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


var DEFAULT_COLUMNS_COUNT = 1;
var useIsomorphicLayoutEffect = typeof window !== "undefined" ? react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect : react__WEBPACK_IMPORTED_MODULE_0__.useEffect;

var useHasMounted = function useHasMounted() {
  var _useState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false),
      hasMounted = _useState[0],
      setHasMounted = _useState[1];

  useIsomorphicLayoutEffect(function () {
    setHasMounted(true);
  }, []);
  return hasMounted;
};

var useWindowWidth = function useWindowWidth() {
  var hasMounted = useHasMounted();

  var _useState2 = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(0),
      width = _useState2[0],
      setWidth = _useState2[1];

  var handleResize = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function () {
    if (!hasMounted) return;
    setWidth(window.innerWidth);
  }, [hasMounted]);
  useIsomorphicLayoutEffect(function () {
    if (hasMounted) {
      window.addEventListener("resize", handleResize);
      handleResize();
      return function () {
        return window.removeEventListener("resize", handleResize);
      };
    }
  }, [hasMounted, handleResize]);
  return width;
};

var MasonryResponsive = function MasonryResponsive(_ref) {
  var _ref$columnsCountBrea = _ref.columnsCountBreakPoints,
      columnsCountBreakPoints = _ref$columnsCountBrea === void 0 ? {
    350: 1,
    750: 2,
    900: 3
  } : _ref$columnsCountBrea,
      children = _ref.children,
      _ref$className = _ref.className,
      className = _ref$className === void 0 ? null : _ref$className,
      _ref$style = _ref.style,
      style = _ref$style === void 0 ? null : _ref$style;
  var windowWidth = useWindowWidth();
  var columnsCount = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () {
    var breakPoints = Object.keys(columnsCountBreakPoints).sort(function (a, b) {
      return a - b;
    });
    var count = breakPoints.length > 0 ? columnsCountBreakPoints[breakPoints[0]] : DEFAULT_COLUMNS_COUNT;
    breakPoints.forEach(function (breakPoint) {
      if (breakPoint < windowWidth) {
        count = columnsCountBreakPoints[breakPoint];
      }
    });
    return count;
  }, [windowWidth, columnsCountBreakPoints]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: className,
    style: style
  }, react__WEBPACK_IMPORTED_MODULE_0___default().Children.map(children, function (child, index) {
    return react__WEBPACK_IMPORTED_MODULE_0___default().cloneElement(child, {
      key: index,
      columnsCount: columnsCount
    });
  }));
};

MasonryResponsive.propTypes =  true ? {
  children: prop_types__WEBPACK_IMPORTED_MODULE_1___default().oneOfType([prop_types__WEBPACK_IMPORTED_MODULE_1___default().arrayOf((prop_types__WEBPACK_IMPORTED_MODULE_1___default().node)), (prop_types__WEBPACK_IMPORTED_MODULE_1___default().node)]).isRequired,
  columnsCountBreakPoints: (prop_types__WEBPACK_IMPORTED_MODULE_1___default().object),
  className: (prop_types__WEBPACK_IMPORTED_MODULE_1___default().string),
  style: (prop_types__WEBPACK_IMPORTED_MODULE_1___default().object)
} : 0;
/* harmony default export */ __webpack_exports__["default"] = (MasonryResponsive);

/***/ }),

/***/ "./node_modules/react-responsive-masonry/es/index.js":
/*!***********************************************************!*\
  !*** ./node_modules/react-responsive-masonry/es/index.js ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ResponsiveMasonry": function() { return /* reexport safe */ _ResponsiveMasonry__WEBPACK_IMPORTED_MODULE_1__["default"]; }
/* harmony export */ });
/* harmony import */ var _Masonry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Masonry */ "./node_modules/react-responsive-masonry/es/Masonry/index.js");
/* harmony import */ var _ResponsiveMasonry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ResponsiveMasonry */ "./node_modules/react-responsive-masonry/es/ResponsiveMasonry/index.js");


/* harmony default export */ __webpack_exports__["default"] = (_Masonry__WEBPACK_IMPORTED_MODULE_0__["default"]);


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

"use strict";
module.exports = window["React"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

"use strict";
module.exports = window["wp"]["element"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
/*!********************************!*\
  !*** ./src/elementor/index.js ***!
  \********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ImportLayoutModal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ImportLayoutModal */ "./src/elementor/ImportLayoutModal.js");


/* global wp */
const {
  render
} = wp.element;

var templateAddSection = jQuery("#tmpl-elementor-add-section");

if (0 < templateAddSection.length) {
  var oldTemplateButton = templateAddSection.html();
  var log_path = rttpgParams.plugin_url + '/assets/images/icon-40x40.svg';
  oldTemplateButton = oldTemplateButton.replace('<div class="elementor-add-section-drag-title', '<div class="elementor-add-section-area-button rttpg-import-button" style="vertical-align: bottom;margin-left: 5px;padding:0"><img src="' + log_path + '" alt="TPG"/></div><div class="elementor-add-section-drag-title');
  templateAddSection.html(oldTemplateButton);

  function replaceKey(obj, targetKey, newKey) {
    for (let key in obj) {
      if (key === targetKey) {
        obj[newKey] = obj[key];
        delete obj[key];
      } else if (typeof obj[key] === "object") {
        replaceKey(obj[key], targetKey, newKey);
      }
    }

    return obj;
  }

  function replaceVal(obj, targetKey, returnVal) {
    for (let key in obj) {
      if (key === targetKey) {
        obj[targetKey] = returnVal || {}; //obj[key];
      } else if (typeof obj[key] === "object") {
        replaceVal(obj[key], targetKey, returnVal);
      }
    }

    return obj;
  }

  const importLayout = (content, insertIndex) => {
    let newContent = JSON.parse(content);
    let modifiedData = newContent.map(item => {
      return { ...item,
        id: Math.random().toString(16).slice(2)
      };
    });
    replaceVal(modifiedData, "category_ids", "");
    replaceVal(modifiedData, 'orderby', 'date');
    replaceVal(modifiedData, 'order', 'DESC');
    replaceVal(modifiedData, 'offset', '');

    if (undefined != insertIndex && insertIndex != -1) {
      if (insertIndex > 1) {
        insertIndex -= 1;
      }

      elementor.getPreviewView().addChildModel(modifiedData, {
        at: insertIndex,
        silent: 0
      });
    } else {
      elementor.getPreviewView().addChildModel(modifiedData, {
        silent: 0
      });
    }

    elementor.channels.data.trigger('template:after:insert', {});

    if (undefined != $e && 'undefined' != typeof $e.internal) {
      $e.internal('document/save/set-is-modified', {
        status: true
      });
    } else {
      elementor.saver.setFlagEditorChange(true);
    }

    window.tmPromo.destroy(); // window.tmPromo.hide();
  };

  wp.domReady(function () {
    elementor.on("preview:loaded", function () {
      jQuery(elementor.$previewContents[0].body).on("click", ".rttpg-import-button", function (event) {
        const insertIndex = 0 < jQuery(this).parents(".elementor-section-wrap").length ? jQuery(this).parents(".elementor-add-section").index() : -1;
        window.tmPromo = elementorCommon.dialogsManager.createWidget("lightbox", {
          id: "rttpg-import-template-wrapper",
          headerMessage: !1,
          message: "",
          hide: {
            auto: !1,
            onClick: !1,
            onOutsideClick: false,
            onOutsideContextMenu: !1,
            onBackgroundClick: !0
          },
          position: {
            my: "center",
            at: "center"
          },
          onShow: function () {
            var contentTemp = jQuery(".dialog-content-tempromo");
            const rttpgImportWrap = document.createElement("div");
            rttpgImportWrap.classList.add("rttpg-import-wrapper-inner");
            render((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_ImportLayoutModal__WEBPACK_IMPORTED_MODULE_1__["default"], {
              importLayout: importLayout,
              insertIndex: insertIndex
            }), rttpgImportWrap);
            contentTemp.addClass('rttpg-import-main-wrapper');
            contentTemp.html(rttpgImportWrap);
          },
          onHide: function () {
            window.tmPromo.hide();
          }
        });
        window.tmPromo.getElements("header").remove();
        window.tmPromo.getElements("message").append(window.tmPromo.addElement("content-tempromo"));
        window.tmPromo.show();
      });
    });
  });
}
}();
/******/ })()
;
//# sourceMappingURL=main.js.map