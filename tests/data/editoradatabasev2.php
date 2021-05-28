<?php

$data = [
 'attributes_params' => true, //params in json value for new editora. Default false
 'truncate_users' => false, //force delete user in database
 'users' => [
 // [name, type, default lang, rol_id, O|U] ("O" Omatech-super-admin or "U" normal user)
 ['omatech', 'Omatech', 'ca', 1, 'O'],
 ['admin', 'Administrator', 'ca', 2, 'U']
 ],
 /**
 * Default roles as 1 and 2
 * roles array add new roles from editora with classes restictions
 */
 'roles' => [
 //['id' => 3, 'name' => 'testrole', 'classes' => '10,20,30'],
 ],

 //languages for editora content
 'languages' => [
 10000 => 'ca',
 20000 => 'es',
 30000 => 'en',
 ],
 //menu groups
 'groups' => [
 /* List all classes numeric order
 * 1,10,11,12,13,14,15,16,17,18
 * 20,21,22,23,24,25,26,27,28,29
 *
 * 40,41,42,43,44,
 *
 * 60,61,62,63,64,65,66,67,68,69,
 * 70,71,72,73,74,75,76,77,78,79
 * 80,81,82,83,84,85,86,88,89,
 * 90,91
 * 100,101,102,103,104,105,106,107,108,109
 * 110,111,112,113,114
 */

 /* List all classes with niceurl
 * 10,12,13,14,15,16,17,18,20,21,23,28,29,40,41,43,44,64,91,90 + 60 + 88
 */
 1 => [
 'Principal',//internal name
 'caption' => ['Principal', 'Principal', 'Principal'],//Caption in CAT, ESP, ENG
 'classes' => [
 1 => [//ID from class: Id 1 is reserved from Global class
 'Global',
 'caption' => ['Global'],//Caption in all languages
 'attributes' => ['200,305,105,106,107,108,116,311'],//attributes from class
 'relations' => [//relations with other classes
 //relation_id => [internal name, childs=>id classes, caption=> caption in CAT, ESP, ENG]
 1008 => ['login', 'childs' => '60', 'caption' => ['Menú usuari', 'Menú usuario', 'Menu user']],
 1009 => ['buy_tickets', 'childs' => '13', 'caption' => ['Comprar entrades', 'Comprar entradas', 'Buy tickets']],
 1001 => ['main_menu', 'childs' => '11', 'caption' => ['Menú principal', 'Menú principal', 'Main menu']],
 1004 => ['submenu', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['SubMenú', 'SubMenú', 'SubMenu']],
 1002 => ['footer_menu', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64,88,90,91', 'caption' => ['Menú peu', 'Menú pie', 'Footer menu']],
 1006 => ['footer_address', 'childs' => '69', 'caption' => ['Direccions', 'Direcciones', 'Addresses']],
 1005 => ['footer_collaborators', 'childs' => '68', 'caption' => ['Colaboradors', 'Colaboradores', 'Collaborators']],
 10012 => ['footer_collaborators_2', 'childs' => '68', 'caption' => ['Colaboradors 2', 'Colaboradores 2', 'Collaborators 2']],
 10013 => ['footer_collaborators_3', 'childs' => '68', 'caption' => ['Colaboradors 3', 'Colaboradores 3', 'Collaborators 3']],
 1007 => ['footer_collaborators_link', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['Colaboradors Link', 'Colaboradores Link', 'Collaborators Link']],
 1003 => ['secondary_footer_menu', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['Menú peu secundari', 'Menú pie secundario', 'Footer secondary menu']],
 10011 => ['page_search', 'childs' => '16', 'caption' => ['Buscador', 'Buscador', 'Page Search']],
 10010 => ['link_404', 'childs' => '88,60', 'caption' => ['Link 404', 'Link 404', 'Link 404']],
 ],
 'editable' => false //mark as editable class. Default true. If false users cannot create or delete, only edit
 ],
 10 => [//ID from class: Id 10 is reserved from Home class
 'Home',
 'caption' => ['Home'],
 'attributes' => ['2,200'],
 'relations' => [
 10002 => ['carrousel', 'childs' => '21,82', 'caption' => ['Carrousel', 'Carrousel', 'Carrousel']],
 10001 => ['blocks', 'childs' => '20,23,24,67,70,71,72,114', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'editable' => false, //mark as editable class. Default true
 'seo_options' => true,//add new tab in editora with seo attributes
 ],
 11 => [
 'SectionPages',
 'caption' => ['Secció Pàgines', 'Sección Páginas', 'Section Pages'],
 'attributes' => ['200'],
 'relations' => [
 11001 => ['pages', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,28,29,40,41,43,44,64,91,90', 'caption' => ['Pàgines', 'Páginas', 'Pages']],
 ],
 'seo_options' => false,
 ],
 12 => [
 'PageGeneric',
 'caption' => ['Pàgina Generica', 'Página Generica', 'Page Generic'],
 'attributes' => ['2,200,201,601,223'],
 'relations' => [
 12001 => ['blocks', 'childs' => '70,71,75,77', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 13 => [
 'PageTickets',
 'caption' => ['Pàgina Entrades', 'Página Entradas', 'Page Tickets'],
 'attributes' => ['2,200'],
 'relations' => [
 13001 => ['blocks', 'childs' => '27,70,71,75,77', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 14 => [
 'PageSubscriptions',
 'caption' => ['Pàgina Abonaments', 'Página Abonos', 'Page Subscriptions'],
 'attributes' => ['2,200'],
 'relations' => [
 14001 => ['blocks', 'childs' => '70,71,75,77', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 16 => [
 'PageSearch',
 'caption' => ['Pàgina Buscador', 'Página Buscador', 'Page Searcher'],
 'attributes' => ['2,200'],
 'relations' => [],
 'seo_options' => true,
 ],
 17 => [
 'PageSeasons',
 'caption' => ['Pàgina Temporades', 'Página Temporadas', 'Page Seasons'],
 'attributes' => ['2,200'],
 'relations' => [],
 'seo_options' => true,
 ],
 15 => [
 'PageContact',
 'caption' => ['Pàgina Contacte', 'Página Contacto', 'Page Contact'],
 'attributes' => ['2,200,311,104,202,307,100,101,105,106,107'],
 'relations' => [
 15001 => ['blocks', 'childs' => '70', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 18 => [
 'PageContactInfoPublic',
 'caption' => ['Pàgina Contacte Info Publica', 'Página Contacto Info Publica', 'Page Contact Info Public'],
 'attributes' => ['2,200,301,311,104,318,319,300'],
 'seo_options' => true,
 ],
 ],
 ],
 2 => [
 'Shows',
 'caption' => ['Espectacles', 'Espectáculos', 'Shows'],
 'classes' => [
 24 => [
 'SectionActivities',
 'caption' => ['Secció Activitats', 'Sección Actividades', 'Section Activities'],
 'attributes' => ['200'],
 'relations' => [
 24001 => ['sections_shows', 'childs' => '20', 'caption' => ['Seccións espectacles', 'Secciones espectaculos', 'Section shows']],
 ],
 'seo_options' => false,
 ],
 20 => [
 'SectionShows',
 'caption' => ['Secció Espectacles', 'Sección Espectáculos', 'Section Show'],
 'attributes' => ['2,200,309,201,300,615,226'],
 'relations' => [
 20001 => ['shows', 'childs' => '21', 'caption' => ['Espectacles', 'Espectaculos', 'Shows']],
 20002 => ['blocks', 'childs' => '70,71', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 21 => [
 'Show',
 'caption' => ['Espectacle', 'Espectáculs', 'Show'],
 'attributes' => ['2,117,882,111,112,877,875,200,308,301,300,309,870,110,606,616,613,781,752,209,801,307,210,214,211,213,306,212,216,217,312,313,314,315,316,317,205,227,226,'],
 'relations' => [
 21001 => ['season', 'childs' => '22', 'caption' => ['Temporada', 'Temporada', 'Season']],
 21007 => ['tags', 'childs' => '64', 'caption' => ['Tags', 'Tags', 'Tags']],
 21002 => ['space', 'childs' => '43', 'caption' => ['Espai/Sala', 'Espai/Sala', 'Espai/Sala']],
 21003 => ['accessibility', 'childs' => '26', 'caption' => ['Accessibilitat', 'Accesibilidad', 'Accessibility']],
 21004 => ['files', 'childs' => '63', 'caption' => ['Arxius', 'Archivos', 'Files']],
 21005 => ['art_sheet', 'childs' => '25', 'caption' => ['Fitxa artistica', 'Ficha artística', 'Art sheet']],
 21006 => ['multimedia', 'childs' => '62,80,81', 'caption' => ['Multimedia', 'Multimedia', 'Multimedia']],
 21010 => ['page_diary', 'childs' => '23', 'caption' => ['Pàgina diari', 'Página diario', 'Page diary']],
 21008 => ['others_shows', 'childs' => '21', 'caption' => ['Altres espectacles', 'Otros espectaculos', 'Others shows']],
 21009 => ['blocks', 'childs' => '71', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 64 => [
 'Tag',
 'caption' => ['Tag', 'Tag', 'Tag'],
 'attributes' => ['2,103,200']
 ],
 22 => [
 'Season',
 'caption' => ['Temporada', 'Temporada', 'Season'],
 'attributes' => ['200,103'],
 'relations' => [],
 'seo_options' => false,
 ],
 43 => [
 'Space',
 'caption' => ['Espai/Sala', 'Espai/Sala', 'Espai/Sala'],
 'attributes' => ['2,200,103,300,220,502,100,620,601,226,223'],
 'relations' => [
 43001 => ['blocks', 'childs' => '70,71,75,77', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 25 => [
 'ArtSheet',
 'caption' => ['Fitxa Artística', 'Ficha Artística', 'Art Sheet'],
 'attributes' => ['200'],
 'relations' => [
 25001 => ['column_1', 'childs' => '61', 'caption' => ['Columna 1', 'Columna 1', 'Column 1']],
 25002 => ['column_2', 'childs' => '61', 'caption' => ['Columna 2', 'Columna 2', 'Column 2']],
 25003 => ['column_3', 'childs' => '61', 'caption' => ['Columna 3', 'Columna 3', 'Column 3']],
 25004 => ['column_2_hide', 'childs' => '61', 'caption' => ['Columna 2 Ocult', 'Columna 1 Oculto', 'Column 1 Hide']],
 25005 => ['column_3_hide', 'childs' => '61', 'caption' => ['Columna 3 Ocult', 'Columna 2 Oculto', 'Column 2 Hide']],
 ],
 'seo_options' => false,
 ],
 26 => [
 'ElementAccessibility',
 'caption' => ['Element Accessibilitat', 'Elemento Accesibilidad', 'Element Accessibility'],
 'attributes' => ['200,876'],
 'seo_options' => false,
 ],
 80 => [
 'MultimediaImage',
 'caption' => ['Multimedia Imatge', 'Multimedia Imagen', 'Multimedia Image'],
 'attributes' => ['617,205']
 ],
 81 => [
 'MultimediaVideo',
 'caption' => ['Multimedia Video', 'Multimedia Video', 'Multimedia Video'],
 'attributes' => ['200,617,780,205']
 ],
 ],
 ],
 3 => [
 'ShowDiary',
 'caption' => ['Espec. Diari dassaig', 'Espec. Diario ensayo', 'Shows Diary'],
 'classes' => [
 28 => [
 'SectionDiary',
 'caption' => ['Secció Diari Assaig', 'Sección Diario Ensayo', 'Section Diary'],
 'attributes' => ['2,200'],
 'relations' => [
 28001 => ['shows', 'childs' => '21', 'caption' => ['Espectacles', 'Espectaculos', 'Shows']],
 ],
 'seo_options' => true,
 ],
 23 => [
 'PageDiary',
 'caption' => ['Pàgina Diari Assaig', 'Página Diario Ensayo', 'Page Diary'],
 'attributes' => ['2,200'],
 'relations' => [],
 'seo_options' => true,
 ],
 29 => [
 'DetailDiary',
 'caption' => ['Detall Diari Assaig', 'Detalle Diario Ensayo', 'Detail Diary'],
 'attributes' => ['2,200,113,115,606,309,621,226,205'],
 'relations' => [
 29001 => ['show', 'childs' => '21', 'caption' => ['Espectacle', 'Espectaculo', 'Show']],
 29002 => ['blocks', 'childs' => '73,76', 'caption' => ['Blocks', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 114 => [
 'BlockDetailsDiary',
 'caption' => ['Bloc Detalls Diari Assaig', 'Bloque Detalles Diario Ensayo', 'Block Details Diary'],
 'attributes' => ['200'],
 'relations' => [
 114001 => ['details_diary', 'childs' => '29', 'caption' => ['Detalls Diari Assaig', 'Detalles Diario Ensayo', 'Details Diary']],
 114002 => ['section_diary', 'childs' => '28', 'caption' => ['Secció Diari Assaig', 'Sección Diario Ensayo', 'Section Diary']],
 ],
 ]
 ]
 ],
 4 => [
 'Theaters',
 'caption' => ['Teatres', 'Teatros', 'Theaters'],
 'classes' => [
 40 => [
 'SectionTheaters',
 'caption' => ['Secció Teatres', 'Sección Teatros', 'Section Theaters'],
 'attributes' => ['2,200'],
 'relations' => [
 40001 => ['blocks', 'childs' => '41,70,71', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 41 => [
 'Theater',
 'caption' => ['Teatre', 'Teatro', 'Theater'],
 'attributes' => ['2,200,201,503,502,100,101,881,601,223'],
 'relations' => [
 41004 => ['info', 'childs' => '61', 'caption' => ['Informació', 'Información', 'Information']],
 41001 => ['timetables', 'childs' => '42', 'caption' => ['Horaris', 'Horarios', 'Timetables']],
 41005 => ['blocks', 'childs' => '75,77', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 44 => [
 'Restaurant',
 'caption' => ['Restaurant', 'Restaurante', 'Restaurant'],
 'attributes' => ['2,200,881'],
 'relations' => [
 44001 => ['blocks', 'childs' => '70,71,75,77', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 44002 => ['breadcrumbs', 'childs' => '41', 'caption' => ['Breadcrumb', 'Breadcrumb', 'Breadcrumb']],
 ],
 'seo_options' => true,
 ],
 42 => [
 'Timetable',
 'caption' => ['Horari', 'Horario', 'Timetable'],
 'attributes' => ['200'],
 'relations' => [
 42001 => ['texts', 'childs' => '61', 'caption' => ['Texts', 'Textos', 'Texts']],
 ],
 'seo_options' => false,
 ],
 ]
 ],
 5 => [
 'Blocks',//internal name
 'caption' => ['Blocs / Banners', 'Bloques / Banners', 'Blocks / Banners'],//Caption in CAT, ESP, ENG
 'classes' => [
 27 => [
 'SectionListShows',
 'caption' => ['Secció Llista Espectacles', 'Sección Lista Espectaculos', 'Section List Shows'],
 'attributes' => ['207,200'],
 ],
 75 => [
 'SectionBlocks',
 'caption' => ['Secció Blocs', 'Sección Bloques', 'Section Blocks'],
 'attributes' => ['207,200'],
 'relations' => [
 75001 => ['blocks', 'childs' => '73,76', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 ],
 73 => [
 'BlockColumnMultimedia',
 'caption' => ['Bloc Columna Multimedia', 'Bloque Columna Multimedia', 'Block Columna Multimedia'],
 'attributes' => ['200'],
 'relations' => [
 73001 => ['elements', 'childs' => '60,62,83,84,85,86,88,74,78,100,101,102,103,104,105,110,112', 'caption' => ['Elements', 'Elementos', 'Elements']],
 73002 => ['lateral', 'childs' => '61,86', 'caption' => ['Lateral', 'Lateral', 'Lateral']],
 ],
 ],
 76 => [
 'BlockMultimedia',
 'caption' => ['Bloc Multimedia', 'Bloque Multimedia', 'Block Multimedia'],
 'attributes' => ['200'],
 'relations' => [
 76001 => ['elements', 'childs' => '20,42,60,62,83,84,85,86,88,74,78,100,101,102,103,104,105,107,110,112', 'caption' => ['Elements', 'Elementos', 'Elements']],
 ],
 ],
 74 => [
 'GroupGenericText',
 'caption' => ['Grup Texts Generic', 'Grup Texto Generico', 'Group Generic Text'],
 'attributes' => ['200'],
 'relations' => [
 74001 => ['texts', 'childs' => '89', 'caption' => ['Texts', 'Textos', 'Texts']],
 ],
 ],
 77 => [
 'SectionBanners',
 'caption' => ['Secció Banners', 'Sección Banners', 'Section Banners'],
 'attributes' => ['207'],
 'relations' => [
 77001 => ['blocks', 'childs' => '70', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 ],
 70 => [
 'BannerInfo',
 'caption' => ['Banner Info', 'Banner Info', 'Banner Info'],
 'attributes' => ['200,300,204,871,872,873,874,610,224,609,225'],
 'relations' => [
 70001 => ['files', 'childs' => '63', 'caption' => ['Arxius', 'Archivos', 'Files']],
 70002 => ['destinationpage', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 71 => [
 'BannerInfoMultiple',
 'caption' => ['Banner Info Multiple', 'Banner Info Multiple', 'Banner Info Multiple'],
 'attributes' => ['871'],
 'relations' => [
 71001 => ['elementsbanner', 'childs' => '65', 'caption' => ['Elements Banner', 'Elementos Banner', 'Elements banner']],
 ],
 ],
 72 => [
 'BannerInfoText',
 'caption' => ['Banner Info Text', 'Banner Info Texto', 'Banner Info Texto'],
 'attributes' => [''],
 'relations' => [
 72001 => ['elementstext', 'childs' => '66', 'caption' => ['Elements Text', 'Elementos Texto', 'Elements Text']],
 ],
 ],
 ],
 ],
 6 => [
 'Elements',//internal name
 'caption' => ['Elements', 'Elementos', 'Elements'],//Caption in CAT, ESP, ENG
 'classes' => [
 88 => [
 'InternalLink',
 'caption' => ['Link Intern', 'Link Interno', 'Internal Link'],
 'attributes' => ['200'],
 'relations' => [
 88001 => ['destinationpage', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,64', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 60 => [
 'ExternalLink',
 'caption' => ['Link Extern', 'Link Externo', 'External Link'],
 'attributes' => ['200,750,800'],
 ],
 65 => [
 'ElementBanner',
 'caption' => ['Element Banner', 'Elemento Banner', 'Element Banner'],
 'attributes' => ['200,300,204,872,873,874,612,611,224,225'],
 'relations' => [
 65001 => ['destinationpage', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 66 => [
 'ElementText',
 'caption' => ['Element Text', 'Elemento Texto', 'Element Text'],
 'attributes' => ['200,300,204'],
 'relations' => [
 66001 => ['destinationpage', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 82 => [
 'ElementCarrousel',
 'caption' => ['Element Carrousel', 'Elemento Carrousel', 'Element Carrousel'],
 'attributes' => ['877,613,780,203,308,751,801,109,209,110,227'],
 'relations' => [
 82001 => ['elementcarrousel_tags', 'childs' => '64', 'caption' => ['Tags', 'Tags', 'Tags']],
 82002 => ['destinationpage', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,64', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 83 => [
 'ListDropdown',
 'caption' => ['Llista Dropdown', 'Lista Dropdown', 'List Dropdown'],
 'attributes' => [''],
 'relations' => [
 83001 => ['elements_dropdown', 'childs' => '108', 'caption' => ['Elements dropdown', 'Elementos dropdown', 'Elements dropdown']],
 ],
 ],
 84 => [
 'SliderImages',
 'caption' => ['Slider Imatges', 'Slider Imagenes', 'Slider Images'],
 'attributes' => ['200'],
 'relations' => [
 84001 => ['images', 'childs' => '62', 'caption' => ['Images', 'Imagenes', 'Imatges']],
 ],
 ],
 85 => [
 'ListFeaturedElements',
 'caption' => ['Llista Elements Destacats', 'Lista Elementos Destacados', 'List Featured Elements'],
 'attributes' => [''],
 'relations' => [
 85001 => ['featured_element', 'childs' => '109', 'caption' => ['Elements destacats', 'Elementos destacados', 'Elements featureds']],
 ],
 ],
 109 => [
 'FeaturedElement',
 'caption' => ['Element Destacat', 'Elemento Destacado', 'Featured Element'],
 'attributes' => ['200,201,218,219,204'],
 'relations' => [
 109001 => ['element', 'childs' => '60,63,88', 'caption' => ['Element', 'Elemento', 'Element']],
 ],
 ],
 110 => [
 'ListFeaturedText',
 'caption' => ['Llista Texts Destacats', 'Lista Textos Destacados', 'List Featured Texts'],
 'attributes' => [''],
 'relations' => [
 110001 => ['featured_texts', 'childs' => '111', 'caption' => ['Texts destacats', 'Textos destacados', 'Texts featureds']],
 ],
 ],
 111 => [
 'FeaturedText',
 'caption' => ['Text Destacat', 'Texto Destacado', 'Text Element'],
 'attributes' => ['200,300,218,884']
 ],
 78 => [
 'ListCards',
 'caption' => ['Llista Targetes', 'Lista Tarjetas', 'List Cards'],
 'attributes' => [''],
 'relations' => [
 78001 => ['cards', 'childs' => '79', 'caption' => ['Targetes', 'Tarjetas', 'Cards']],
 ],
 ],
 79 => [
 'Card',
 'caption' => ['Targeta', 'Tarjeta', 'Card'],
 'attributes' => ['200,300,204,218,219,221,222'],
 'relations' => [
 79001 => ['destinationpage', 'childs' => '60,88', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 86 => [
 'ListElements',
 'caption' => ['Llista Elements', 'Lista Elementos', 'List Elements'],
 'attributes' => ['200,201,300,204'],
 'relations' => [
 86001 => ['elements', 'childs' => '60,63,86,88', 'caption' => ['Elements', 'Elementos', 'Elements']],
 ],
 ],
 89 => [
 'GenericText',
 'caption' => ['Generic Text', 'Generico Text', 'Generic Text'],
 'attributes' => ['302,310,300'],
 ],
 103 => [
 'GalleryImages',
 'caption' => ['Galeria Imatges', 'Galeria Imagenes', 'Galeria Images'],
 'attributes' => ['200,880,883'],
 'relations' => [
 103001 => ['images', 'childs' => '62,68,80', 'caption' => ['Imatge', 'Imagen', 'Image']],
 ],
 ],
 104 => [
 'ListButtons',
 'caption' => ['Llista Botons', 'Lista Botones', 'List Buttons'],
 'attributes' => [''],
 'relations' => [
 104001 => ['links', 'childs' => '60,88', 'caption' => ['Links', 'Links', 'Links']],
 ],
 ],
 112 => [
 'ListIcons',
 'caption' => ['Llista Icones', 'Lista Iconos', 'List Icons'],
 'attributes' => ['300'],
 'relations' => [
 112001 => ['elements_icons', 'childs' => '113', 'caption' => ['Elements icona', 'Elementos icono', 'Elements icon']],
 ],
 ],
 113 => [
 'ElementIcon',
 'caption' => ['Element Icona', 'Elemento Icono', 'Element Icon'],
 'attributes' => ['300,874,885,884'],
 ],
 105 => [
 'ElementTable',
 'caption' => ['Taula', 'Tabla', 'Table'],
 'attributes' => [''],
 'relations' => [
 105001 => ['title_columns', 'childs' => '61', 'caption' => ['Columnes títol', 'Columnas título', 'Columns title']],
 105002 => ['rows', 'childs' => '106', 'caption' => ['Files', 'Filas', 'Rows']],
 ],
 ],
 106 => [
 'RowTable',
 'caption' => ['Fila Tabla', 'Fila Tabla', 'Row Table'],
 'attributes' => ['200'],
 'relations' => [
 106001 => ['texts', 'childs' => '61', 'caption' => ['Texts', 'Textos', 'Texts']],
 ],
 ],
 107 => [
 'GroupSpace',
 'caption' => ['Grup Espai/Sala', 'Grupo Espacio/Sala', 'Group Space'],
 'attributes' => [''],
 'relations' => [
 107001 => ['spaces', 'childs' => '43', 'caption' => ['Espais/Salas', 'Espacios/Salas', 'Spaces']],
 ],
 ],
 ],
 ],
 7 => [
 'ElementsSecondary',
 'caption' => ['Elements secundaris', 'Elementos secundarios', 'Secondary elements'],
 'classes' => [
 67 => [
 'Alert',
 'captions' => ['Alerta', 'Alerta', 'Alert'],
 'attributes' => ['200,300,204'],
 'relations' => [
 67001 => ['destinationpage', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 61 => [
 'Text',
 'caption' => ['Text', 'Texto', 'Text'],
 'attributes' => ['200,300'],
 ],
 62 => [
 'Image',
 'caption' => ['Image', 'Imagen', 'Image'],
 'attributes' => ['604,205,215'],
 ],
 102 => [
 'Video',
 'caption' => ['Video', 'Video', 'Video'],
 'attributes' => ['200,750,800,619,205'],
 'relations' => [],
 ],
 63 => [
 'File',
 'caption' => ['Arxiu', 'Archivo', 'File'],
 'attributes' => ['200,201,760,770']
 ],
 68 => [
 'Collaborator',
 'caption' => ['Collaborador', 'Colaborador', 'Collaborator'],
 'attributes' => ['605,205,750,800']
 ],
 69 => [
 'Address',
 'caption' => ['Direcció', 'Dirección', 'Address'],
 'attributes' => ['100,200,301,306'],
 'relations' => [
 69001 => ['destinationpage', 'childs' => '10,12,13,14,15,16,17,18,20,21,23,40,41,43,44,60,64', 'caption' => ['Destination Page', 'Destination Page', 'Destination Page']],
 ],
 ],
 101 => [
 'ElementColumnInfo',
 'caption' => ['Element Columna Info', 'Elemento Columna Info', 'Element Column Info'],
 'attributes' => ['879,618,215,300,205'],
 'relations' => [],
 ],
 100 => [
 'ElementContact',
 'caption' => ['Element Contacte', 'Elemento Contacto', 'Element Contact'],
 'attributes' => ['200,100,201,101'],
 ],
 108 => [
 'ElementDropdown',
 'caption' => ['Element Dropdown', 'Elemento Dropdown', 'Element Dropdown'],
 'attributes' => ['200'],
 'relations' => [
 108001 => ['items', 'childs' => '61,62,84,85,101,102,112', 'caption' => ['Items', 'Items', 'Items']],
 ],
 ],
 ]
 ],
 9 => [
 'PressRoom',
 'caption' => ['Premsa', 'Prensa', 'Press'],
 'classes' => [
 91 => [
 'SectionPressRoom',
 'caption' => ['Secció Sala Premsa', 'Sección Sala Prensa', 'Section Press Room'],
 'attributes' => ['2,200'],
 'relations' => [
 91001 => ['press_rooms', 'childs' => '90', 'caption' => ['', 'Items', 'Items']],
 ],
 'seo_options' => true,
 ],
 90 => [
 'PressRoom',
 'caption' => ['Sala Premsa', 'Sala Prensa', 'Press Room'],
 'attributes' => ['2,200,300,606,601,226,223'],
 'relations' => [
 90001 => ['blocks', 'childs' => '70,71,75,77', 'caption' => ['Blocs', 'Bloques', 'Blocks']],
 ],
 'seo_options' => true,
 ],
 ],
 ]
 ],
 'seo_attributes' => ['900,901,910,911,912,913'], //attributes for seo tab in editora

 //attributes_multi_lang_xxx insert attributes in all languages defined in array languages

 'attributes_order_string' => [//special string attribute with database index for extraction
 103 => ['tag', 'caption' => ['Tag', 'Tag', 'Tag']],
 ],
 'attributes_order_date' => [//special date attribute with database index for extraction
 102 => ['news_date', 'caption' => ['Data noticia', 'Fecha noticia', 'News Date']],
 111 => ['date_start_show', 'caption' => ['Data espectacle inici', 'Fecha espectaculo inicio', 'Start show Date']],
 112 => ['date_finish_show', 'caption' => ['Data espectacle fi', 'Fecha espectaculo final', 'Finish show Date']],
 113 => ['date_publish', 'caption' => ['Data publicat', 'Fecha publicado', 'Publish date']],
 ],
 'attributes_string' => [
 100 => ['phone', 'caption' => ['Telèfon', 'Teléfono', 'Phone']],
 101 => ['email', 'caption' => ['E-mail', 'E-mail', 'E-mail']],
 104 => ['email_responsible', 'caption' => ['E-mail responsable', 'E-mail responsable', 'E-mail responsible']],
 105 => ['facebook', 'caption' => ['Facebook', 'Facebook', 'Facebook']],
 106 => ['twitter', 'caption' => ['Twitter', 'Twitter', 'Twitter']],
 107 => ['instagram', 'caption' => ['Instagram', 'Instagram', 'Instagram']],
 108 => ['youtube', 'caption' => ['YouTube', 'YouTube', 'YouTube']],
 109 => ['period', 'caption' => ['Periode', 'Periodo', 'Period']],
 110 => ['room', 'caption' => ['Sala', 'Sala', 'Room']],
 115 => ['publish_text', 'caption' => ['Publicat text', 'Publicación texto', 'Publish text']],
 116 => ['email_responsible_newsletter', 'caption' => ['E-mail responsable newsletter', 'E-mail responsable newsletter', 'E-mail responsible newsletter']],
 117 => ['product_code_secutix', 'caption' => ['Producte CODI Secutix', 'Producto CODIGO Secutix', 'Product CODE Secutix']],
 ],
 'attributes_multi_lang_string' => [
 200 => ['title', 'caption' => ['Títol', 'Título', 'Title']],
 201 => ['subtitle', 'caption' => ['Subtítol', 'Subtítulo', 'Subtitle']],
 202 => ['title_intro', 'caption' => ['Títol intro', 'Título intro', 'Title intro']],
 203 => ['pretitle', 'caption' => ['Pre-títol', 'Pre-título', 'Pre-title']],
 204 => ['text_calltoaction', 'caption' => ['Text botó', 'Texto botón', 'Text call to action']],
 205 => ['alt_img', 'caption' => ['Alt img', 'Alt img', 'Alt img']],
 206 => ['supertitle', 'caption' => ['SuperTítol', 'SuperTítulo', 'SuperTitle']],
 207 => ['title_anchor', 'caption' => ['Títol anchor', 'Título anchor', 'Title anchor']],
 209 => ['period', 'caption' => ['Periode ca', 'Periodo es', 'Period en']],
 210 => ['duration', 'caption' => ['Durada', 'Duración', 'Duration']],
 211 => ['language', 'caption' => ['Idioma', 'Idioma', 'Language']],
 212 => ['colloquium', 'caption' => ['Colloqui', 'Coloquio', 'Colloquium']],
 213 => ['price', 'caption' => ['Preu', 'Precio', 'Price']],
 214 => ['place', 'caption' => ['Lloc', 'Luegar', 'Place']],
 215 => ['footer_photo', 'caption' => ['Peu de foto', 'Pie de foto', 'Footer photo']],
 216 => ['text_cancelled', 'caption' => ['Informació cancellat', 'Información cancelado', 'Information cancelled']],
 217 => ['text_finalized', 'caption' => ['Informació finalitzat', 'Informació finalizado', 'Information finalized']],
 218 => ['extra_info_title', 'caption' => ['Extra info titol', 'Extra info titulo', 'Extra info title']],
 219 => ['extra_info_subtitle', 'caption' => ['Extra info subtitol', 'Extra info subtitulo', 'Extra info subtitle']],
 220 => ['capacity', 'caption' => ['Capacitat', 'Capacidad', 'Capacity']],
 221 => ['extra_secondary_info_title', 'caption' => ['Extra secundari info titol', 'Extra secundario info titulo', 'Extra secondary info title']],
 222 => ['extra_secondary_info_subtitle', 'caption' => ['Extra secundari info subtitol', 'Extra secundario info subtitulo', 'Extra secondary info subtitle']],

 223 => ['alt_img_header', 'caption' => ['Alt img capçalera', 'Alt img cabecera', 'Alt img header'], 'params' => ['size' => ['']]],
 224 => ['alt_img_icon', 'caption' => ['Alt img icona', 'Alt img icono', 'Alt img icon'], 'params' => ['size' => ['']]],
 225 => ['alt_img_background', 'caption' => ['Alt img background', 'Alt img background', 'Alt img background'], 'params' => ['size' => ['']]],
 226 => ['alt_img_thumbnail', 'caption' => ['Alt img thumbnail', 'Alt img thumbnail', 'Alt img thumbnail'], 'params' => ['size' => ['']]],
 227 => ['alt_img_carrousel', 'caption' => ['Alt img carrousel', 'Alt img carrousel', 'Alt img carrousel'], 'params' => ['size' => ['']]],

 //SEO Attributes
 910 => ['meta_title', 'caption' => ['Meta Title', 'Meta Title', 'Meta Title']],
 913 => ['alt_og_image', 'caption' => ['Alt og imatge', 'Alt og imagen', 'Alt og image']],
 ],
 'attributes_textarea' => [
 500 => ['text', 'caption' => ['text ca', 'text es', 'text en']],
 501 => ['intro', 'caption' => ['intro ca', 'intro es', 'intro en']],
 502 => ['address', 'caption' => ['Direcció', 'Dirección', 'Address']],
 503 => ['iframe_map', 'caption' => ['Iframe mapa', 'Iframe mapa', 'Iframe map']],
 ],
 'attributes_multi_lang_textarea' => [
 300 => ['text', 'caption' => ['Text', 'Texto', 'Text']],
 301 => ['text_intro', 'caption' => ['Text intro', 'Texto intro', 'Text intro']],
 302 => ['text_header', 'caption' => ['Text capçalera', 'Texto cabecerea', 'Text header']],
 303 => ['subtext_header', 'caption' => ['Subtext capçalera', 'Subtexto cabecerea', 'Subtext header']],
 304 => ['text_footer', 'caption' => ['Text footer', 'Texto footer', 'Text footer']],
 305 => ['text_cookies', 'caption' => ['Text cookies', 'Texto cookies', 'Text cookies']],
 306 => ['text_accessibility', 'caption' => ['Text accessibilitat', 'Texto accesibilidad', 'Text accessibility']],
 307 => ['timetable', 'caption' => ['Horari', 'Horario', 'Timetable']],
 308 => ['text_from', 'caption' => ['Text capçalera obra', 'Texto cabecera obra', 'Text header show']],
 309 => ['text_thumbnail', 'caption' => ['Text thumbnail', 'Texto thumbnail', 'Text thumbnail']],
 310 => ['quote', 'caption' => ['Cita', 'Cita', 'Quote']],
 311 => ['privacy_policy', 'caption' => ['Política de privacitat', 'Política de privacidad', 'Privacy Policy']],
 312 => ['text_from_data', 'caption' => ['Text from data', 'Texto from fecha', 'Text from data']],
 313 => ['column_1', 'caption' => ['Columna 1', 'Columna 1', 'Column 1']],
 314 => ['column_2', 'caption' => ['Columna 2', 'Columna 2', 'Column 2']],
 315 => ['column_3', 'caption' => ['Columna 3', 'Columna 3', 'Column 3']],
 316 => ['column_2_hide', 'caption' => ['Columna 2 Ocult', 'Columna 1 Oculto', 'Column 1 Hide']],
 317 => ['column_3_hide', 'caption' => ['Columna 3 Ocult', 'Columna 2 Oculto', 'Column 2 Hide']],
 318 => ['privacy_policy_2', 'caption' => ['Política de privacitat 2', 'Política de privacidad 2', 'Privacy Policy 2']],
 319 => ['privacy_policy_3', 'caption' => ['Política de privacitat 3', 'Política de privacidad 3', 'Privacy Policy 3']],

 //SEO Attributes
 911 => ['meta_keywords', 'caption' => ['Meta Keywords', 'Meta Keywords', 'Meta Keywords']],
 912 => ['meta_description', 'caption' => ['Meta Description', 'Meta Description', 'Meta Description']],
 ],
 'attributes_text' => [],
 'attributes_multi_lang_text' => [],
 'attributes_date' => [],
 'attributes_color' => [],//paint color selector and insert color
 'attributes_num' => [],
 'attributes_geolocation' => [
 745 => ['map', 'caption' => ['Mapa', 'Mapa', 'Map']]
 ],//add google map searcher and save geolocation attributes
 'attributes_url' => [
 750 => ['url', 'caption' => ['url', 'url', 'url']],
 751 => ['url_tickets', 'caption' => ['Tickets', 'Tickets', 'Tickets']],
 752 => ['url_video_carrousel_live', 'caption' => ['Video url carrousel live', 'Video url carrousel live', 'Video url carrousel live']],

 ],
 'attributes_multi_lang_url' => [
 800 => ['url', 'caption' => ['url ca', 'url es', 'url en']],
 801 => ['url_tickets', 'caption' => ['Tickets ca', 'Tickets es', 'Tickets en']],
 ],
 'attributes_file' => [
 760 => ['file', 'caption' => ['Arxiu', 'Archivo', 'File']]
 ],
 'attributes_multi_lang_file' => [
 770 => ['file', 'caption' => ['Arxiu ca', 'Archivo es', 'File en']]
 ],
 'attributes_video' => [//insert url from youtube or vimeo and save id from video and service
 780 => ['video', 'caption' => ['Video', 'Video', 'Video']],
 781 => ['video_carrousel', 'caption' => ['Video carrousel', 'Video carrousel', 'Video carrousel']],
 ],
 'attributes_multi_lang_video' => [],
 'attributes_image' => [
 600 => ['news_image', 'caption' => ['Imatge noticia', 'Imagen noticia', 'News image'], 'params' => []],
 601 => ['img_header', 'caption' => ['Imatge capçalera', 'Imágen cabecera', 'Header Picture'], 'params' => ['size' => ['']]],
 602 => ['img_logo_header', 'caption' => ['Imatge Logo Capçalera', 'Imágen Logo Cabecera', 'Header Logo Image'], 'params' => ['size' => ['']]],
 603 => ['img_logo_footer', 'caption' => ['Imatge Logo Footer', 'Imágen Logo Footer', 'Footer Logo Image'], 'params' => ['size' => ['']]],
 604 => ['img_image', 'caption' => ['Imatge', 'Imágen', 'Image'], 'params' => ['size' => ['']]],
 605 => ['img_collaborator', 'caption' => ['Imatge', 'Imágen', 'Image'], 'params' => ['size' => ['']]],
 606 => ['img_thumbnail', 'caption' => ['Imatge thumbnail', 'Imagen thumbnail', 'Image thumbnail'], 'params' => ['size' => ['338x']]],
 607 => ['img_thumbnail_third', 'caption' => ['Imatge thumbnail', 'Imagen thumbnail', 'Image thumbnail'], 'params' => ['size' => ['457x']]],
 608 => ['img_thumbnail_second', 'caption' => ['Imatge thumbnail', 'Imagen thumbnail', 'Image thumbnail'], 'params' => ['size' => ['695x']]],
 609 => ['img_bannerinfo_background', 'caption' => ['Imatge background', 'Imagen background', 'Image background'], 'params' => ['size' => ['']]],
 610 => ['img_bannerinfo_icon', 'caption' => ['Imatge icon', 'Imagen icon', 'Image icon'], 'params' => ['size' => ['211x202']]],
 611 => ['img_elementbanner_background', 'caption' => ['Imatge background', 'Imagen background', 'Image background'], 'params' => ['size' => ['']]],
 612 => ['img_elementbanner_icon', 'caption' => ['Imatge icon', 'Imagen icon', 'Image icon'], 'params' => ['size' => ['211x202']]],
 613 => ['img_carrousel', 'caption' => ['Imatge carrousel', 'Imagen carrousel', 'Image carrousel'], 'params' => ['size' => ['']]],
 614 => ['img_pagegeneric', 'caption' => ['Imatge', 'Imagen', 'Image'], 'params' => ['size' => ['']]],
 615 => ['img_sectionshow_thumbnail', 'caption' => ['Imatge thumbnail', 'Imagen thumbnail', 'Image thumbnail'], 'params' => ['size' => ['']]],
 616 => ['img_show', 'caption' => ['Imatge', 'Imagen', 'Image'], 'params' => ['size' => ['']]],
 617 => ['img_multimediashow', 'caption' => ['Imatge', 'Imagen', 'Image'], 'params' => ['size' => ['']]],
 618 => ['img_columninfo', 'caption' => ['Imatge', 'Imagen', 'Image'], 'params' => ['size' => ['']]],
 619 => ['img_video', 'caption' => ['Imatge', 'Imagen', 'Image'], 'params' => ['size' => ['']]],
 620 => ['img_space_thumbnail', 'caption' => ['Imatge thumbnail', 'Imagen thumbnail', 'Image thumbnail'], 'params' => ['size' => ['']]],
 621 => ['img_detail_diary', 'caption' => ['Imatge', 'Imagen', 'Image'], 'params' => ['size' => ['']]],

 //SEO Attributes
 901 => ['og_image', 'caption' => ['Imatge og', 'Imagen og', 'facebook og'], 'params' => ['size' => ['']]],
 ],
 'attributes_multi_lang_image' => [],
 'attributes_grid_image' => [//preview image with grid with positions for width and heigth
 ],
 'attributes_lookup' => [//attribute lookup with options
 //SEO Attributes
 870 => ['type_web_show',
 'caption' => ['Tipus web espectacle', 'Tipo web espectaculo', 'Show web type'],
 'params' => [
 'lookup' => [
 8701 => ['new_web', 'Nova web', 'Nueva web', 'New web'],
 8702 => ['old_web', 'Web antiga', 'Web antigua', 'Old web'],
 8703 => ['old_web_rwd', 'Web antiga rwd', 'Web antigua rwd', 'Old web rwd'],
 ]
 ]
 ],
 871 => ['color_text',
 'caption' => ['Color text', 'Color texto', 'Color text'],
 'params' => [
 'lookup' => [
 8711 => ['dark', 'Negre', 'Negro', 'Black'],
 8712 => ['white', 'Blanc', 'Blanco', 'White'],
 ]
 ]
 ],
 872 => ['position_text',
 'caption' => ['Posició text', 'Posición texto', 'Position text'],
 'params' => [
 'lookup' => [
 8721 => ['center', 'Centre', 'Centro', 'Center'],
 8722 => ['left', 'Esquerra', 'Izquierda', 'Left'],
 8723 => ['right', 'Dreta', 'Derecha', 'Right'],
 ]
 ]
 ],
 873 => ['size_text',
 'caption' => ['Tamany text', 'Tamaño texto', 'Size text'],
 'params' => [
 'lookup' => [
 8731 => ['2', 'Normal', 'Normal', 'Normal'],
 8732 => ['1', 'Gran', 'Grande', 'Big'],
 ]
 ]
 ],
 874 => ['icon',
 'caption' => ['Icona', 'Icono', 'Icon'],
 'params' => [
 'lookup' => [
 8741 => [null, '--', '--', '--'],
 8747 => ['ctrl-audio','ctrl-audio','ctrl-audio','ctrl-audio'],
 8748 => ['ctrl-pause','ctrl-pause','ctrl-pause','ctrl-pause'],
 8749 => ['ctrl-play','ctrl-play','ctrl-play','ctrl-play'],
 8746 => ['audition','audition','audition','audition'],
 8745 => ['mobility','mobility','mobility','mobility'],
 87410 => ['play','play','play','play'],
 87411 => ['instagram','instagram','instagram','instagram'],
 87412 => ['access','access','access','access'],
 87413 => ['a-ears','a-ears','a-ears','a-ears'],
 87414 => ['a-hands','a-hands','a-hands','a-hands'],
 87415 => ['arrow','arrow','arrow','arrow'],
 87416 => ['a-subtit','a-subtit','a-subtit','a-subtit'],
 87417 => ['calendar','calendar','calendar','calendar'],
 87418 => ['chat','chat','chat','chat'],
 87419 => ['chat-post','chat-post','chat-post','chat-post'],
 87420 => ['chat-pre','chat-pre','chat-pre','chat-pre'],
 87421 => ['check','check','check','check'],
 87422 => ['chevron-dw','chevron-dw','chevron-dw','chevron-dw'],
 87423 => ['chevron-lt','chevron-lt','chevron-lt','chevron-lt'],
 87424 => ['chevron-rt','chevron-rt','chevron-rt','chevron-rt'],
 87425 => ['chevron-up','chevron-up','chevron-up','chevron-up'],
 87426 => ['close','close','close','close'],
 87427 => ['doc','doc','doc','doc'],
 8742 => ['down','down','down','down'],
 87428 => ['email','email','email','email'],
 87429 => ['expand','expand','expand','expand'],
 87430 => ['facebook','facebook','facebook','facebook'],
 87431 => ['filter','filter','filter','filter'],
 87432 => ['hand','hand','hand','hand'],
 87433 => ['link','link','link','link'],
 87434 => ['menu','menu','menu','menu'],
 87435 => ['search','search','search','search'],
 87436 => ['share','share','share','share'],
 8744 => ['sofa','sofa','sofa','sofa'],
 87437 => ['swipe-lt','swipe-lt','swipe-lt','swipe-lt'],
 87438 => ['swipe-rt','swipe-rt','swipe-rt','swipe-rt'],
 87439 => ['swipe-up','swipe-up','swipe-up','swipe-up'],
 87440 => ['tick','tick','tick','tick'],
 8743 => ['ticket','ticket','ticket','ticket'],
 87441 => ['tri-dw','tri-dw','tri-dw','tri-dw'],
 87442 => ['tri-full-dw','tri-full-dw','tri-full-dw','tri-full-dw'],
 87443 => ['tri-full-lt','tri-full-lt','tri-full-lt','tri-full-lt'],
 87444 => ['tri-full-rt','tri-full-rt','tri-full-rt','tri-full-rt'],
 87445 => ['tri-full-up','tri-full-up','tri-full-up','tri-full-up'],
 87446 => ['tri-lt','tri-lt','tri-lt','tri-lt'],
 87447 => ['tri-rt','tri-rt','tri-rt','tri-rt'],
 87448 => ['tri-up','tri-up','tri-up','tri-up'],
 87449 => ['twitter','twitter','twitter','twitter'],
 87450 => ['user','user','user','user'],
 87451 => ['whatsapp','whatsapp','whatsapp','whatsapp'],
 87452 => ['youtube','youtube','youtube','youtube'],
 ]
 ]
 ],
 875 => ['state_show',
 'caption' => ['Estat', 'Estado', 'State'],
 'params' => [
 'lookup' => [
 8751 => ['active', 'Actiu', 'Activo', 'Active'],
 8752 => ['finalized', 'Finalitzat', 'Finalizado', 'Finalized'],
 8753 => ['cancelled', 'Cancellat', 'Cancelado', 'Cancelled'],
 ]
 ]
 ],
 876 => ['accessibility_show',
 'caption' => ['Estat', 'Estado', 'State'],
 'params' => [
 'lookup' => [
 8764 => ['ctrl-audio','ctrl-audio','ctrl-audio','ctrl-audio'],
 8765 => ['ctrl-pause','ctrl-pause','ctrl-pause','ctrl-pause'],
 8766 => ['ctrl-play','ctrl-play','ctrl-play','ctrl-play'],
 8767 => ['audition','audition','audition','audition'],
 8768 => ['mobility','mobility','mobility','mobility'],
 8769 => ['play','play','play','play'],
 87610 => ['instagram','instagram','instagram','instagram'],
 87611 => ['access','access','access','access'],
 8763 => ['a-ears','a-ears','a-ears','a-ears'],
 8762 => ['a-hands','a-hands','a-hands','a-hands'],
 87612 => ['arrow','arrow','arrow','arrow'],
 8761 => ['a-subtit','a-subtit','a-subtit','a-subtit'],
 87613 => ['calendar','calendar','calendar','calendar'],
 87614 => ['chat','chat','chat','chat'],
 87615 => ['chat-post','chat-post','chat-post','chat-post'],
 87616 => ['chat-pre','chat-pre','chat-pre','chat-pre'],
 87617 => ['check','check','check','check'],
 87618 => ['chevron-dw','chevron-dw','chevron-dw','chevron-dw'],
 87619 => ['chevron-lt','chevron-lt','chevron-lt','chevron-lt'],
 87620 => ['chevron-rt','chevron-rt','chevron-rt','chevron-rt'],
 87621 => ['chevron-up','chevron-up','chevron-up','chevron-up'],
 87622 => ['close','close','close','close'],
 87623 => ['doc','doc','doc','doc'],
 87624 => ['down','down','down','down'],
 87625 => ['email','email','email','email'],
 87626 => ['expand','expand','expand','expand'],
 87627 => ['facebook','facebook','facebook','facebook'],
 87628 => ['filter','filter','filter','filter'],
 87629 => ['hand','hand','hand','hand'],
 87630 => ['link','link','link','link'],
 87631 => ['menu','menu','menu','menu'],
 87632 => ['search','search','search','search'],
 87633 => ['share','share','share','share'],
 87634 => ['sofa','sofa','sofa','sofa'],
 87635 => ['swipe-lt','swipe-lt','swipe-lt','swipe-lt'],
 87636 => ['swipe-rt','swipe-rt','swipe-rt','swipe-rt'],
 87637 => ['swipe-up','swipe-up','swipe-up','swipe-up'],
 87638 => ['tick','tick','tick','tick'],
 87639 => ['ticket','ticket','ticket','ticket'],
 87640 => ['tri-dw','tri-dw','tri-dw','tri-dw'],
 87641 => ['tri-full-dw','tri-full-dw','tri-full-dw','tri-full-dw'],
 87642 => ['tri-full-lt','tri-full-lt','tri-full-lt','tri-full-lt'],
 87643 => ['tri-full-rt','tri-full-rt','tri-full-rt','tri-full-rt'],
 87644 => ['tri-full-up','tri-full-up','tri-full-up','tri-full-up'],
 87645 => ['tri-lt','tri-lt','tri-lt','tri-lt'],
 87646 => ['tri-rt','tri-rt','tri-rt','tri-rt'],
 87647 => ['tri-up','tri-up','tri-up','tri-up'],
 87648 => ['twitter','twitter','twitter','twitter'],
 87649 => ['user','user','user','user'],
 87650 => ['whatsapp','whatsapp','whatsapp','whatsapp'],
 87651 => ['youtube','youtube','youtube','youtube'],
 ]
 ]
 ],
 877 => ['color_carrousel',
 'caption' => ['Color fons carrousel', 'Color fondo carrousel', 'Color background carrousel'],
 'params' => [
 'lookup' => [
 8771 => ['white', 'blanc', 'blanco', 'white'],
 8772 => ['black', 'negre', 'negro', 'black'],
 ]
 ]
 ],
 878 => ['color_background',
 'caption' => ['Color fons', 'Color fondo', 'Color background'],
 'params' => [
 'lookup' => [
 8781 => ['dark', 'Negre', 'Negro', 'Black'],
 8782 => ['white', 'Blanc', 'Blanco', 'White'],
 ]
 ]
 ],
 879 => ['position_column_image',
 'caption' => ['Posició text', 'Posición texto', 'Position text'],
 'params' => [
 'lookup' => [
 8791 => ['left', 'Esquerra', 'Izquierda', 'Left'],
 8792 => ['right', 'Dreta', 'Derecha', 'Right'],
 ]
 ]
 ],
 880 => ['columns_images',
 'caption' => ['Columnes', 'Columnas', 'Columns'],
 'params' => [
 'lookup' => [
 8801 => ['three', '3', '3', '3'],
 8802 => ['four', '4', '4', '4'],
 ]
 ]
 ],
 881 => ['position_text_header',
 'caption' => ['Posició text capçalera', 'Posición texto cabecera', 'Position text header'],
 'params' => [
 'lookup' => [
 8811 => [null, 'Default', 'Default', 'Default'],
 8812 => ['head-center', 'Centrat', 'Centrado', 'Center'],
 ]
 ]
 ],
 882 => ['type_show',
 'caption' => ['Tipus espectacle', 'Tipo espectaculo', 'Type show'],
 'params' => [
 'lookup' => [
 8821 => ['show', 'Obra', 'Obra', 'Show'],
 8822 => ['activity', 'Activitat', 'Actividad', 'Activity'],
 ]
 ]
 ],
 883 => ['centered_image',
 'caption' => ['Imatge centrada', 'Imagen centrada', 'Center image'],
 'params' => [
 'lookup' => [
 8831 => ['filling-pic', 'No', 'No', 'No'],
 8832 => [null, 'Si', 'Si', 'Yes'],
 ]
 ]
 ],
 884 => ['featured_text',
 'caption' => ['Text destacat', 'Texto destacado', 'Featured text'],
 'params' => [
 'lookup' => [
 8841 => [null, 'No', 'No', 'No'],
 8842 => ['imp', 'Si', 'Si', 'Yes'],
 ]
 ]
 ],
 885 => ['type_icon',
 'caption' => ['Icona tipus', 'Icono tipo', 'Type icon'],
 'params' => [
 'lookup' => [
 8851 => [null, 'Normal', 'Normal', 'Normal'],
 8852 => ['featured', 'Destacat', 'Destacado', 'Featured'],
 8853 => ['featured_alert', 'Destacat vermell', 'Destacado rojo', 'Featured red'],
 ]
 ]
 ],
 900 => ['meta_robots',
 'caption' => ['Meta Robots', 'Meta Robots', 'Meta Robots'],
 'params' => [
 'lookup' => [
 9001 => ['index,follow', 'index,follow', 'index,follow', 'index,follow'],
 9002 => ['noindex,nofollow', 'noindex,nofollow', 'noindex,nofollow', 'noindex,nofollow'],
 9003 => ['index,nofollow', 'index,nofollow', 'index,nofollow', 'index,nofollow'],
 9004 => ['noindex,follow', 'noindex,follow', 'noindex,follow', 'noindex,follow']
 ]
 ]
 ]
 ],

];
