

import layout from './layout/layout.vue';
import container from './layout/container.vue';

import listMood from './components/moods/list-moods.vue';
import moodDetail from './components/moods/mood-detail.vue';

import settingUser from './setting/user.vue';
import settingPasswordReset from './setting/passwordReset.vue';
import settingDeleteAccount from './setting/deleteAccount.vue';

import navigationLeft from './layout/navigationLeft.vue';
import navigationLeftHome from './components/navigations/left/home.vue';
import navigationLeftSetting from './components/navigations/left/settings.vue';

import view_login from './login/login.vue';
import view_logon from './logon/logon.vue';


const routes = [
                    { 
                        path: '/', 
                        component: layout,
                        children: [
                            {
                                path: '',
                                components:{ 
                                    navigationLeftRoot: navigationLeft,
                                    contentRoot: container
                                },
                                children: [
                                    {
                                        path:'',
                                        components: {
                                            navigationLeft: navigationLeftHome,
                                            content: listMood
                                        }
                                    }
                                ]
                            },
                            {
                                path: 'mood/:id',
                                components:{ 
                                    navigationLeftRoot: navigationLeft,
                                    contentRoot: container
                                },
                                children: [
                                    {
                                        path:'',
                                        components: {
                                            navigationLeft: navigationLeftHome,
                                            content: moodDetail
                                        }
                                    }
                                ]
                            }
                        ]
                    },
                    { 
                        path: '/setting', 
                        component: layout,
                        children: [
                            {
                                path: '',
                                components:{ 
                                    navigationLeftRoot: navigationLeft,
                                    contentRoot: container
                                },
                                children: [
                                    {
                                        path:'',
                                        components: {
                                            navigationLeft: navigationLeftSetting,
                                            content: settingUser
                                        },
                                        meta: { requiresAuth: true }
                                    },
                                    {
                                        path:'passwordReset',
                                        components: {
                                            navigationLeft: navigationLeftSetting,
                                            content: settingPasswordReset
                                        },
                                        meta: { requiresAuth: true }
                                    },
                                    {
                                        path:'deleteAccount',
                                        components: {
                                            navigationLeft: navigationLeftSetting,
                                            content: settingDeleteAccount
                                        },
                                        meta: { requiresAuth: true }
                                    }
                                ]
                            }
                        ]
                    },
                    { path: '/login', component: view_login },
                    { path: '/logon', component: view_logon }
               ]

export default routes