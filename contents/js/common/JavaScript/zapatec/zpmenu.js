/**
 * @fileoverview Zapatec Menu widget. Include this file in your HTML page.
 * Includes base Zapatec Menu modules: zpmenu-core.js. To extend menu with other
 * features like keyboard navigation, include respective modules manually in
 * your HTML page.
 *
 * <pre>
 * Copyright (c) 2004-2007 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 * </pre>
 */

/* $Id: zpmenu.js 6286 2007-02-14 15:33:51Z alex $ */

/**
 * Path to Zapatec Menu scripts.
 * @private
 */
Zapatec.menuPath = Zapatec.getPath('Zapatec.MenuWidget');

// Include required scripts
Zapatec.include(Zapatec.menuPath + 'zpmenu-core.js', 'Zapatec.Menu');