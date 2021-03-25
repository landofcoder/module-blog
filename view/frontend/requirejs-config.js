/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
     paths: {
        'jquery.Isotope': 'Ves_Blog/js/isotope.min',
        'jquery.Bridget': 'Ves_Blog/js/jquery-bridget'
    },
 	shim: {
        'Ves_Blog/js/jquery.shorten.min': {
            'deps': ['jquery']
        },
        'Ves_Blog/js/jquery.shorten': {
            'deps': ['jquery']
        }
    }
 };