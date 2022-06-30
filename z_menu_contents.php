<?php
// cms content administration
// adoptable to user tables
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$layout
	->set('title',L_CONTENTS)
	->set('content',
		'<ul class="icons">'.
		($dbi->checkUserPermission('system')
			? createListItem(L_TEXTBLOCKS,'z_list_textblocks','textblocks')
			: NULL
		).
		($dbi->checkUserPermission('view')
			? createListItem('Victims','nmv_list_victims','')
				. createListItem('Prisoner Assistants', 'nmv_list_prisoner_assistants', '')
				//complete db d
				.($dbi->checkUserPermission('mpg')
						? NULL
						: createListItem('Biomedical Research','nmv_list_experiments','')
					)
			  . createListItem('Perpetrators','nmv_list_perpetrators','')
			  . createListItem('Institutions','nmv_list_institutions','')
			  . createListItem('Literature','nmv_list_literature','')
			  . createListItem('Sources','nmv_list_sources','')
			: NULL
		).
		($dbi->checkUserPermission('admin')
			?
				'<br><li><strong>Reference Tables:</strong></li>'
				.	createListItem('Behaviour','nmv_list_behaviour','')
				. createListItem('Classification (Imprisonment)','nmv_list_victim_classification','')
				. createListItem('Classification (Experiment)','nmv_list_experiment_classification','')
				. createListItem('Classification (Perpetrator)','nmv_list_perpetrator_classification','')
				. createListItem('Country','nmv_list_country','')
				. createListItem('Dataset Origin','nmv_list_dataset_origin','')
				. createListItem('Diagnosis','nmv_list_diagnosis','')
				. createListItem('Disability','nmv_list_disability','')
				. createListItem('Education (Victim)','nmv_list_education','')
				. createListItem('Educational Abilities (Hospitalisation)','nmv_list_educational_abilities','')
				. createListItem('Ethnic Group','nmv_list_ethnicgroup','')
				. createListItem('Evaluation Status','nmv_list_victim_evaluation_status','')
				. createListItem('Institution Type','nmv_list_institution_type','')
				. createListItem('Marital / Family Status','nmv_list_marital_family_status','')
				. createListItem('Language','nmv_list_language','')
				. createListItem('Nametype','nmv_list_victim_nametype','')
				. createListItem('Nationality','nmv_list_nationality','')
				. createListItem('Occupation (Victim)','nmv_list_occupation','')
				. createListItem('Order of Institution (Hospitalisation)','nmv_list_institution_order','')
				. createListItem('Religion','nmv_list_religion','')
				. createListItem('Role','nmv_list_role','')
				. createListItem('Survival','nmv_list_survival','')
				. createListItem('Tissue Form','nmv_list_tissue_form','')
				. createListItem('Tissue State','nmv_list_tissue_state','')

			: NULL
		).
		'</ul>'.
		createHomeLink ())
	->set('sidebar',$dbi->getTextblock_HTML ('z_contents'))
	->cast();
