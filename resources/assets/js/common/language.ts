declare function require(name:string);

/**
 * Import Dependency
 */

import VueI18n from 'vue-i18n';

/**
 * Import Language
 * 
 * import en from './i18n/en_Es';
 * import it from './i18n/it_IT';
 */

var en = require('../../i18n/en_Es.json');
var it = require('../../i18n/it_IT.json');

/**
 * Export
 */
export default class Language{

	language: string;
	messages;

	public vueI18n: VueI18n;

	constructor(){

		this.language = 'it';
		this.messages = it;

		this.vueI18n = new VueI18n({
			locale: this.language,
			messages: this.messages
		})

		console.log(this.vueI18n);
	}

}


