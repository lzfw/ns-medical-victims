<?php

$institution_query_start =
"SELECT DISTINCT i.ID_institution, i.institution_name, i.location,
                c.english AS country, t.english AS institution_type, i.notes
FROM nmv__institution i
LEFT JOIN nmv__country c ON c.ID_country = i.ID_country
LEFT JOIN nmv__institution_type t ON t.ID_institution_type = i.type";
$institution_query_end = "GROUP BY i.ID_institution";


$source_query_start =
"SELECT DISTINCT s.ID_source, s.source_title, s.signature, s.creation_year,
                s.pages, s.type, s.language, s.description, s.medium AS medium_old,
                m.medium, IF(s.published_source = -1, 'yes', '-') AS published_source,
                s.location, i.institution_name, s.folder, s.url,
                CONCAT(IFNULL(s.access_day, '-'), '.', IFNULL(s.access_month, '-'), '.', IFNULL(s.access_year, '-')) AS access_date,
                s.notes
FROM nmv__source s
LEFT JOIN nmv__institution i ON i.ID_institution = s.ID_institution
LEFT JOIN nmv__medium m ON m.ID_medium = s.ID_medium";
$source_query_end = "GROUP BY s.ID_source";


$literature_query_start =
"SELECT DISTINCT l.ID_literature, l.lit_title, l.authors, l.editor, l.lit_year, l.publisher,
                l.location, IF(l.article = -1, 'yes', '-') AS is_article, l.journal_or_series,
                l.volume, l.pages, IF(l.scientific_exploitation = 1, 'yes', ' - ') AS scientific_exploitation,
                IF(l.written_by_perpetrator = -1, 'yes', ' - ') AS written_by_perpetrator, l.url,
                CONCAT(IFNULL(l.access_day, '-'), '.', IFNULL(l.access_month, '-'), '.', IFNULL(l.access_year, '-')) AS access_date_url,
                l.isbn, l.doi, l.notes
FROM nmv__literature l";
$literature_query_end = "GROUP BY l.ID_literature";


$perpetrator_query_start =
"SELECT p.ID_perpetrator, p.surname, p.first_names, p.titles,
CONCAT(IFNULL(p.birth_day, '-'), '.', IFNULL(p.birth_month, '-'), '.', IFNULL(p.birth_year, '-')) AS birth_date,
p.birth_place, bc.english AS birth_country,
CONCAT(IFNULL(p.death_day, '-'), '.', IFNULL(p.death_month, '-'), '.', IFNULL(p.death_year, '-')) AS death_date,
p.death_place, dc.english AS death_country,
p.gender, r.english AS religion, n.english AS nationality_1938, p.occupation, pc.english AS perp_classification,
p.career_history,
p.year_of_qualification_1, p.place_of_qualification_1, p.type_of_qualification_1, p.title_of_dissertation_1,
p.year_of_qualification_2, p.place_of_qualification_2, p.type_of_qualification_2, p.title_of_dissertation_2,
IF(p.leopoldina_member = -1, 'yes', '-') AS leopoldina_member, p.leopoldina_since_when AS leopoldina_since,
IF(p.nsdap_member = -1, 'yes', '-') AS nsdap_member, p.nsdap_since_when AS nsdap_since,
IF(p.ss_member = -1, 'yes', '-') AS ss_member, p.ss_since_when AS ss_since,
IF(p.sa_member = -1, 'yes', '-') AS sa_member, p.sa_since_when AS sa_since,
IF(p.other_nsdap_organisations_member = -1, 'yes', '-') AS other_nsdap_organisations_member,
p.details_all_memberships, p.career_after_1945, p.prosecution, p.prison_time, p.notes,
GROUP_CONCAT(DISTINCT
                'ID experiment: --', pe.ID_experiment, '--, ',
                IF(e.experiment_title IS NULL, '', CONCAT('TITLE experiment: --', e.experiment_title, '--'))
                 SEPARATOR ' \n') AS 'Victim - Experiment',
GROUP_CONCAT(DISTINCT
        'ID source: --', s.ID_source, '--, ',
        IF(s.source_title IS NULL, '', CONCAT('TITLE source: --', s.source_title, '--, ')),
        IF(ps.location IS NULL, '', CONCAT('LOCATION in source: --', ps.location, '--, ')),
        IF(ps.url IS NULL, '', CONCAT('URL: --', ps.url, '--, ')),
        IF(ps.access_day IS NULL AND ps.access_month IS NULL AND ps.access_year IS NULL, '', CONCAT('ACCESS DATE URL --', IFNULL(ps.access_day, '-'), '.', IFNULL(ps.access_month, '-'), '.', IFNULL(ps.access_year, '-'), '--, ')),
        IF(ps.source_has_photo = -1, 'source has photo', '')
         SEPARATOR ' \n') AS sources,
GROUP_CONCAT(DISTINCT
        'ID literature: --', l.ID_literature, '--, ',
        IF(l.lit_title IS NULL, '', CONCAT('title literature: --', l.lit_title, '--, ')),
        IF(pl.pages IS NULL, '', CONCAT('pages: --', pl.pages, '--, ')),
        IF(pl.url IS NULL, '', CONCAT('URL: --', pl.url, '--, ')),
        IF(pl.access_day IS NULL AND pl.access_month IS NULL AND pl.access_year IS NULL, '', CONCAT('ACCESS DATE URL --', IFNULL(pl.access_day, '-'), '.', IFNULL(pl.access_month, '-'), '.', IFNULL(pl.access_year, '-'), '--, ')),
        IF(pl.literature_has_photo = -1, 'literature has photo', '')
                 SEPARATOR ' \n') AS literature
FROM nmv__perpetrator p
LEFT JOIN nmv__country bc ON bc.ID_country = p.ID_birth_country
LEFT JOIN nmv__country dc ON dc.ID_country = p.ID_death_country
LEFT JOIN nmv__religion r ON r.ID_religion = p.religion
LEFT JOIN nmv__nationality n ON n.ID_nationality = p.nationality_1938
LEFT JOIN nmv__perpetrator_classification pc ON pc.ID_perp_class = p.ID_perp_class
LEFT JOIN nmv__perpetrator_literature pl ON pl.ID_perpetrator = p.ID_perpetrator
LEFT JOIN nmv__literature l ON l.ID_literature = pl.ID_literature
LEFT JOIN nmv__perpetrator_source ps ON ps.ID_perpetrator = p.ID_perpetrator
LEFT JOIN nmv__source s ON s.ID_source = ps.ID_source
LEFT JOIN nmv__perpetrator_experiment pe ON pe.ID_perpetrator = p.ID_perpetrator
LEFT JOIN nmv__experiment e ON e.ID_experiment = pe.ID_experiment";
$perpetrator_query_end = "GROUP BY p.ID_perpetrator";


$experiment_query_start =
"SELECT e.ID_experiment, e.experiment_title, IF(e.confirmed_experiment = 1, 'yes', ' - ') AS confirmed_experiment,
        ec.english AS classification, e.funding, GROUP_CONCAT(DISTINCT f.english SEPARATOR ', ') AS fields_of_interest,
        e.objective, GROUP_CONCAT(DISTINCT 'ID ', i.ID_institution, ' - ', i.institution_name SEPARATOR ' \n ') AS institution,
        e.location_details, e.notes_location, e.number_victims_estimate, e.number_victims_remark, e.number_fatalities_estimate,
        CONCAT(IFNULL(e.start_day, '-'), '.', IFNULL(e.start_month, '-'), '.', IFNULL(e.start_year, '-')) AS start_date,
        CONCAT(IFNULL(e.end_day, '-'), '.', IFNULL(e.end_month, '-'), '.', IFNULL(e.end_year, '-')) AS end_date, e.notes,
GROUP_CONCAT(DISTINCT
        'ID source: --', s.ID_source, '--, ',
        IF(s.source_title IS NULL, '', CONCAT('TITLE source: --', s.source_title, '--, ')),
        IF(es.location IS NULL, '', CONCAT('LOCATION in source: --', es.location, '--, ')),
        IF(es.url IS NULL, '', CONCAT('URL: --', es.url, '--, ')),
        IF(es.access_day IS NULL AND es.access_month IS NULL AND es.access_year IS NULL, '', CONCAT('ACCESS DATE URL --', IFNULL(es.access_day, '-'), '.', IFNULL(es.access_month, '-'), '.', IFNULL(es.access_year, '-'), '--, '))
         SEPARATOR ' \n') AS sources,
GROUP_CONCAT(DISTINCT
        'ID literature: --', l.ID_literature, '--, ',
        IF(l.lit_title IS NULL, '', CONCAT('title literature: --', l.lit_title, '--, ')),
        IF(el.pages IS NULL, '', CONCAT('pages: --', el.pages, '--, ')),
        IF(el.url IS NULL, '', CONCAT('URL: --', el.url, '--, ')),
        IF(el.access_day IS NULL AND el.access_month IS NULL AND el.access_year IS NULL, '', CONCAT('ACCESS DATE URL --', IFNULL(el.access_day, '-'), '.', IFNULL(el.access_month, '-'), '.', IFNULL(el.access_year, '-'), '--, '))
                 SEPARATOR ' \n') AS literature
FROM nmv__experiment e
LEFT JOIN nmv__experiment_institution ei ON ei.ID_experiment = e.ID_experiment
LEFT JOIN nmv__institution i ON i.ID_institution = ei.ID_institution
LEFT JOIN nmv__experiment_classification ec ON ec.ID_exp_classification = e.classification
LEFT JOIN nmv__experiment_foi ef ON ef.ID_experiment = e.ID_experiment
LEFT JOIN nmv__field_of_interest f ON f.ID_foi = ef.ID_foi
LEFT JOIN nmv__experiment_literature el ON el.ID_experiment = e.ID_experiment
LEFT JOIN nmv__literature l ON l.ID_literature = el.ID_literature
LEFT JOIN nmv__experiment_source es ON es.ID_experiment = e.ID_experiment
LEFT JOIN nmv__source s ON s.ID_source = es.ID_source
LEFT JOIN nmv__perpetrator_experiment pe		ON e.ID_experiment = pe.ID_experiment
LEFT JOIN nmv__perpetrator p								ON pe.ID_perpetrator = p.ID_perpetrator";
$experiment_query_end = "GROUP BY e.ID_experiment";




$victim_query_start =
"SELECT v.ID_victim, v.surname,
GROUP_CONCAT(DISTINCT vn.victim_name, IF(nt.english IS NULL, 'null', CONCAT(' (', nt.english, ')') ) SEPARATOR ', ') AS alternative_surnames,
v.first_names,
GROUP_CONCAT(DISTINCT vn.victim_first_names, IF(nt.english IS NULL, 'null', CONCAT(' (', nt.english, ')') ) SEPARATOR ', ') AS alternative_firstnames, IF(v.twin=-1, 'yes', NULL) AS twin,
IF(v.birth_day IS NULL AND v.birth_month IS NULL AND v.birth_year IS NULL, NULL,
CONCAT(IFNULL(v.birth_day, '-'), '.', IFNULL(v.birth_month, '-'), '.', IFNULL(v.birth_year, '-'))) AS birth_date_DMY,
v.birth_place, bc.english AS birth_country, n1938.english AS nationality_1938,
IF(v.death_day IS NULL AND v.death_month IS NULL AND v.death_year IS NULL, NULL,
CONCAT(IFNULL(v.death_day, '-'), '.', IFNULL(v.death_month, '-'), '.', IFNULL(v.death_year, '-'))) AS death_date_DMY,
v.death_place,
CONCAT(IFNULL(di.institution_name, ''), ' - ', IFNULL(di.location, ''), ' - ', IFNULL(v.death_institution, '')) AS death_institution,
dc.english AS death_country, v.cause_of_death, v.gender, f.english AS family_status, r.english AS religion,
eg.english AS ethnic_group, ed.english AS highest_education, o.english AS occupation, v.occupation_details,
v.arrest_prehistory, v.arrest_location, ac.english AS arrest_country, v.arrest_history,
GROUP_CONCAT(DISTINCT
	       'ID imprisonment: --', i.ID_imprisonment, '--',
		IF(i.location IS NULL, '', CONCAT(', imprisonment location: --', i.location, '--')),
    IF(i.ID_institution IS NULL, '', CONCAT(', institution of imprisonment: --', ii.institution_name, '--')),
		IF(i.number IS NULL, '', CONCAT(', prisoner_number: --', i.number, '--')),
    IF(vicla.classifications IS NULL, '', CONCAT(', prisoner classification(s): --', vicla.classifications, '--')),
		IF(i.start_day IS NULL AND i.start_month IS NULL AND i.start_year IS NULL, '',
      CONCAT(', date of imprisonment DMY: --', IFNULL(i.start_day, '-'), '.', IFNULL(i.start_month, '-'), '.', IFNULL(i.start_year, '-'), '--'))
                 SEPARATOR ' \n') AS  imprisonments,
GROUP_CONCAT(DISTINCT
                'ID experiment: --', ve.ID_experiment, '--, ',
                IF(ex.experiment_title IS NULL, '', CONCAT('TITLE experiment: --', ex.experiment_title, '--, ')),
                IF(ve.experiment_duration IS NULL, '', CONCAT('experiment DURATION for victim: --', ve.experiment_duration, '--, ')),
                IF(ve.age_experiment_start IS NULL, '', CONCAT('AGE of victim at start of experiment: --', ve.age_experiment_start, '--, ')),
                IF(ve.exp_start_day IS NULL AND ve.exp_start_month IS NULL AND ve.exp_start_year IS NULL, '',
                  CONCAT('START of experiment: --', IFNULL(ve.exp_start_day, '-'), '.', IFNULL(ve.exp_start_month, '-'), '.', IFNULL(ve.exp_start_year, '-'), '--, ')),
                IF(ve.exp_end_day IS NULL AND ve.exp_end_month IS NULL AND ve.exp_end_year IS NULL, '',
                  CONCAT('END of experiment: --', IFNULL(ve.exp_end_day, '-'), '.', IFNULL(ve.exp_end_month, '-'), '.', IFNULL(ve.exp_end_year, '-'), '--, ')),
                IF(ve.outcome_injuries IS NULL, '', CONCAT('outcome INJURIES: --', ve.outcome_injuries, '--, ')),
                IF(s.english IS NULL, '', CONCAT('SURVIVAL of THIS experiment: --', s.english, '--, ')),
                IF(ve.notes_perpetrator IS NULL, '', CONCAT('notes about PERPETRATOR: --', ve.notes_perpetrator, '--, ')),
                IF(ve.notes IS NULL, '', CONCAT('NOTES concerning experiment: --', ve.notes, '--, ')),
                IF(ve.narratives IS NULL, '', CONCAT('NARRATIVES concerning experiment: --', ve.narratives, '--'))
                 SEPARATOR ' \n') AS 'Victim - Experiment',
GROUP_CONCAT(DISTINCT
                'ID hospitalisation: --', h.ID_med_history_hosp, '--, ',
                IF(ih.institution_name IS NULL AND h.institution IS NULL, '', CONCAT('INSTITUTION: --', IFNULL(ih.institution_name, ''), ', ', IFNULL(h.institution, ''), '--, ')),
                IF(ioh.english IS NULL, '', CONCAT('institution order: --', ioh.english, '--, ')),
                IF(h.date_entry_day IS NULL AND h.date_entry_month IS NULL AND h.date_entry_year IS NULL, '',
                  CONCAT('Date ENTRY: --', IFNULL(h.date_entry_day, '-'), '.', IFNULL(h.date_entry_month, '-'), '.', IFNULL(h.date_entry_year, '-'), '--, ')),
                IF(h.age_entry IS NULL, '', CONCAT('AGE at entry: --', h.age_entry, '--, ')),
                IF(h.date_exit_day IS NULL AND h.date_exit_month IS NULL AND h.date_exit_year IS NULL, '',
                  CONCAT('Date EXIT: --', IFNULL(h.date_exit_day, '-'), '.', IFNULL(h.date_exit_month, '-'), '.', IFNULL(h.date_exit_year, '-'), '--, ')),
                IF(h.age_exit IS NULL, '', CONCAT('AGE at exit: --', h.age_exit, '--, ')),
                IF(h.diagnosis IS NULL, '', CONCAT('DIAGNOSIS: --', h.diagnosis, '--, ')),
                IF(ditah.hosp_diagnoses IS NULL, '', CONCAT('DIAGNOSIS TAGS: --', ditah.hosp_diagnoses, '--, ')),
                IF(eh.english IS NULL, '', CONCAT('EDUCATIONAL ABILITIES: --', eh.english, '--, ')),
                IF(bh.english IS NULL, '', CONCAT('BEHAVIOUR:-- ', bh.english, '--, ')),
                IF(dih.english IS NULL, '', CONCAT('DISABILITY: --', dih.english, '--, ')),
                IF(h.autopsy_ref_no IS NULL, '', CONCAT('autopsy REF NO: --', h.autopsy_ref_no, '--, ')),
                IF(h.notes IS NULL, '', CONCAT('hospitalisation NOTES: --', h.notes, '--, ')),
                IF(h.hosp_has_photo = -1, 'medical record has photo', '')
                 SEPARATOR ' \n') AS 'Victim - Hospitalisations (Information often from Krankenakten)',
GROUP_CONCAT(DISTINCT
                'ID brain report: --', b.ID_med_history_brain, '--, ',
                IF(ib.institution_name IS NULL, '', CONCAT('INSTITUTION: --', ib.institution_name, '--, ')),
                IF(b.kwi_researcher IS NULL, '', CONCAT('RESEARCHER: --', b.kwi_researcher, '--, ')),
                IF(b.diagnosis IS NULL, '', CONCAT('diagnosis: --', b.diagnosis, '--, ')),
                IF(ditab.brain_diagnoses IS NULL, '', CONCAT('DIAGNOSIS TAGS: --', ditab.brain_diagnoses, '--, ')),
                IF(b.brain_report_day IS NULL AND b.brain_report_month IS NULL AND b.brain_report_year IS NULL, '',
                  CONCAT('DATE of brain report: --', IFNULL(b.brain_report_day, '-'), '.', IFNULL(b.brain_report_month, '-'), '.', IFNULL(b.brain_report_year, '-'), '--, ')),
                IF(b.ref_no IS NULL, '', CONCAT('brain report REF NO: --', b.ref_no, '--, ')),
                IF(b.notes IS NULL, '', CONCAT('brain report NOTES: --', b.notes, '--, ')),
                IF(b.brain_report_has_photo = -1, 'brain report has photo', '')
                 SEPARATOR ' \n') AS 'Victim - Brain Report',
GROUP_CONCAT(DISTINCT
		'ID tissue: --', t.ID_med_history_tissue, '--, ',
                IF(it.institution_name IS NULL, '', CONCAT('INSTITUTION: --', it.institution_name, '--, ')),
                IF(t.since_day IS NULL AND t.since_month IS NULL AND t.since_year IS NULL, '',
                  CONCAT('in institution SINCE: --', IFNULL(t.since_day, '-'), '.', IFNULL(t.since_month, '-'), '.', IFNULL(t.since_year, '-'), '--, ')),
                IF(ts.english IS NULL, '', CONCAT('tissue STATE: --', ts.english, '--, ')),
                IF(tf.english IS NULL, '', CONCAT('tissue FORM: --', tf.english, '--, ')),
                IF(t.ref_no IS NULL, '', CONCAT('tissue REF NO: --', t.ref_no, '--, ')),
                IF(t.notes IS NULL, '', CONCAT('tissue NOTES: --', t.notes, '--, '))
                 SEPARATOR ' \n') AS 'Victim - Tissue',
v.notes, v.consequential_injuries, v.compensation, v.compensation_details, evs.english AS evaluation_status, v.status_due_to, v.status_notes,
v.residence_after_1945_place, v.residence_after_1945_country, n1945.english AS nationality_after_1945, v.occupation_after_1945, v.notes_after_1945,
GROUP_CONCAT(DISTINCT
		'ID source: --', so.ID_source, '--, ',
		IF(so.source_title IS NULL, '', CONCAT('TITLE source: --', so.source_title, '--, ')),
		IF(vs.location IS NULL, '', CONCAT('LOCATION in source: --', vs.location, '--, ')),
                IF(vs.url IS NULL, '', CONCAT('URL: --', vs.url, '--, ')),
                IF(vs.access_day IS NULL AND vs.access_month IS NULL AND vs.access_year IS NULL, '',
                  CONCAT('ACCESS DATE URL --', IFNULL(vs.access_day, '-'), '.', IFNULL(vs.access_month, '-'), '.', IFNULL(vs.access_year, '-'), '--, ')),
                IF(vs.source_has_photo = -1, 'source has photo', '')
                 SEPARATOR ' \n') AS sources,
GROUP_CONCAT(DISTINCT
                'ID literature: --', l.ID_literature, '--, ',
		IF(l.lit_title IS NULL, '', CONCAT('title literature: --', l.lit_title, '--, ')),
		IF(vl.pages IS NULL, '', CONCAT('pages: --', vl.pages, '--, ')),
                IF(vl.url IS NULL, '', CONCAT('URL: --', vl.url, '--, ')),
                IF(vl.access_day IS NULL AND vl.access_month IS NULL AND vl.access_year IS NULL, '',
                  CONCAT('ACCESS DATE URL --', IFNULL(vl.access_day, '-'), '.', IFNULL(vl.access_month, '-'), '.', IFNULL(vl.access_year, '-'), '--, ')),
                IF(vl.literature_has_photo = -1, 'literature has photo', '')
                 SEPARATOR ' \n') AS literature
FROM nmv__victim v
        LEFT JOIN nmv__victim_name vn ON vn.ID_victim = v.ID_victim
        LEFT JOIN nmv__victim_nametype nt ON nt.ID_nametype = vn.nametype
        LEFT JOIN nmv__country bc ON bc.ID_country = v.ID_birth_country
        LEFT JOIN nmv__nationality n1938 ON n1938.ID_nationality = v.nationality_1938
        LEFT JOIN nmv__country dc ON dc.ID_country = v.ID_death_country
        LEFT JOIN nmv__institution di ON di.ID_institution = v.ID_death_institution
        LEFT JOIN nmv__marital_family_status f ON f.ID_marital_family_status = v.ID_marital_family_status
        LEFT JOIN nmv__religion r ON r.ID_religion = v.religion
        LEFT JOIN nmv__ethnicgroup eg ON eg.ID_ethnicgroup = v.ethnic_group
        LEFT JOIN nmv__education ed ON ed.ID_education = v.ID_education
        LEFT JOIN nmv__occupation o ON o.ID_occupation = v.occupation
        LEFT JOIN nmv__country ac ON ac.ID_country = v.ID_arrest_country
        LEFT JOIN nmv__nationality n1945 ON n1945.ID_nationality = v.nationality_after_1945
        LEFT JOIN nmv__victim_experiment ve ON ve.ID_victim = v.ID_victim
        LEFT JOIN nmv__survival s ON s.ID_survival = ve.ID_survival
        LEFT JOIN nmv__experiment ex ON ex.ID_experiment = ve.ID_experiment
        LEFT JOIN nmv__experiment_institution ei ON ei.ID_experiment = ex.ID_experiment
        LEFT JOIN nmv__experiment_foi ef ON ef.ID_experiment = ex.ID_experiment
        LEFT JOIN nmv__imprisonment i	ON v.ID_victim = i.ID_victim
        LEFT JOIN nmv__imprisonment_classification ic ON ic.ID_imprisonment = i.ID_imprisonment
        LEFT JOIN nmv__institution ii ON ii.ID_institution = i.ID_institution
        LEFT JOIN (
                SELECT i1.ID_imprisonment, GROUP_CONCAT(vc.english SEPARATOR ', ') AS classifications
                FROM nmv__imprisonment i1
                LEFT JOIN nmv__imprisonment_classification ic ON ic.ID_imprisonment = i1.ID_imprisonment
                LEFT JOIN nmv__victim_classification vc ON vc.ID_classification = ic.ID_classification
                GROUP BY i1.ID_imprisonment) AS vicla ON vicla.ID_imprisonment = i.ID_imprisonment
        LEFT JOIN nmv__victim_evaluation_status evs ON evs.ID_evaluation_status = v.ID_evaluation_status
        LEFT JOIN nmv__victim_source vs ON vs.ID_victim = v.ID_victim
        LEFT JOIN nmv__source so ON so.ID_source = vs.ID_source
        LEFT JOIN nmv__victim_literature vl ON vl.ID_victim = v.ID_victim
        LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
        LEFT JOIN nmv__med_history_hosp h ON h.ID_victim = v.ID_victim
        LEFT JOIN nmv__diagnosis_hosp dh ON dh.ID_med_history_hosp = h.ID_med_history_hosp
        LEFT JOIN nmv__diagnosis_tag dth ON dth.ID_diagnosis = dh.ID_diagnosis
        LEFT JOIN nmv__institution ih ON ih.ID_institution = h.ID_institution
        LEFT JOIN nmv__institution_order ioh ON ioh.ID_institution_order = h.ID_institution_order
        LEFT JOIN nmv__behaviour bh ON bh.ID_behaviour = h.ID_behaviour
        LEFT JOIN nmv__disability dih ON dih.ID_disability = h.ID_disability
        LEFT JOIN nmv__educational_abilities eh ON eh.ID_educational_abilities = h.ID_educational_abilities
        LEFT JOIN (
                SELECT h1.ID_med_history_hosp, GROUP_CONCAT(dit.diagnosis SEPARATOR ', ') AS hosp_diagnoses
                FROM nmv__med_history_hosp h1
                LEFT JOIN nmv__diagnosis_hosp dth ON dth.ID_med_history_hosp = h1.ID_med_history_hosp
                LEFT JOIN nmv__diagnosis_tag dit ON dit.ID_diagnosis = dth.ID_diagnosis
                GROUP BY h1.ID_med_history_hosp) AS ditah ON ditah.ID_med_history_hosp = h.ID_med_history_hosp
        LEFT JOIN nmv__med_history_brain b ON b.ID_victim = v.ID_victim
        LEFT JOIN nmv__diagnosis_brain db ON db.ID_med_history_brain = b.ID_med_history_brain
        LEFT JOIN nmv__diagnosis_tag dtb			ON dtb.ID_diagnosis = db.ID_diagnosis
        LEFT JOIN nmv__institution ib ON ib.ID_institution = b.ID_institution
        LEFT JOIN (
                SELECT b1.ID_med_history_brain, GROUP_CONCAT(dit.diagnosis SEPARATOR ', ') AS brain_diagnoses
                FROM nmv__med_history_brain b1
                LEFT JOIN nmv__diagnosis_brain dtb ON dtb.ID_med_history_brain = b1.ID_med_history_brain
                LEFT JOIN nmv__diagnosis_tag dit ON dit.ID_diagnosis = dtb.ID_diagnosis
                GROUP BY b1.ID_med_history_brain) AS ditab ON ditab.ID_med_history_brain = b.ID_med_history_brain
        LEFT JOIN nmv__med_history_tissue t ON t.ID_victim = v.ID_victim
        LEFT JOIN nmv__institution it ON it.ID_institution = t.ID_institution
        LEFT JOIN nmv__tissue_state ts ON ts.ID_tissue_state = t.ID_tissue_state
        LEFT JOIN nmv__tissue_form tf ON tf.ID_tissue_form = t.ID_tissue_form
";
$victim_query_end =  " GROUP BY v.ID_victim ORDER BY v.ID_victim ASC";


$was_prisoner_assistant_query_start =
"SELECT v.ID_victim, v.surname, IF(v.was_prisoner_assistant != 'victim only', 'yes', ' - ') AS was_prisoner_assistant, GROUP_CONCAT(DISTINCT vn.victim_name, IF(nt.english IS NULL, 'null', CONCAT(' (', nt.english, ')') ) SEPARATOR ', ') AS alternative_surnames,
v.first_names, GROUP_CONCAT(DISTINCT vn.victim_first_names, IF(nt.english IS NULL, 'null', CONCAT(' (', nt.english, ')') ) SEPARATOR ', ') AS alternative_firstnames, IF(v.twin=-1, 'yes', NULL) AS twin,
IF(v.birth_day IS NULL AND v.birth_month IS NULL AND v.birth_year IS NULL, NULL, CONCAT(IFNULL(v.birth_day, '-'), '.', IFNULL(v.birth_month, '-'), '.', IFNULL(v.birth_year, '-'))) AS birth_date_DMY,
v.birth_place, bc.english AS birth_country, n1938.english AS nationality_1938,
IF(v.death_day IS NULL AND v.death_month IS NULL AND v.death_year IS NULL, NULL, CONCAT(IFNULL(v.death_day, '-'), '.', IFNULL(v.death_month, '-'), '.', IFNULL(v.death_year, '-'))) AS death_date_DMY,
v.death_place,
CONCAT(IFNULL(di.institution_name, ''), ' - ', IFNULL(di.location, ''), ' - ', IFNULL(v.death_institution, '')) AS death_institution,
dc.english AS death_country, v.cause_of_death, v.gender, f.english AS family_status, r.english AS religion,
eg.english AS ethnic_group, ed.english AS highest_education, o.english AS occupation, v.occupation_details,
v.arrest_prehistory, v.arrest_location, ac.english AS arrest_country, v.arrest_history,
GROUP_CONCAT(DISTINCT
               'ID imprisonment: --', i.ID_imprisonment, '--',
                IF(i.location IS NULL, '', CONCAT(', imprisonment location: --', i.location, '--')),
                IF(i.ID_institution IS NULL, '', CONCAT(', institution of imprisonment: --', ii.institution_name, '--')),
                IF(i.number IS NULL, '', CONCAT(', prisoner_number: --', i.number, '--')),
                IF(vicla.classifications IS NULL, '', CONCAT(', prisoner classification(s): --', vicla.classifications, '--')),
                IF(i.start_day IS NULL AND i.start_month IS NULL AND i.start_year IS NULL, '', CONCAT(', date of imprisonment DMY: --', IFNULL(i.start_day, '-'), '.', IFNULL(i.start_month, '-'), '.', IFNULL(i.start_year, '-'), '--'))
                 SEPARATOR ' \n') AS  imprisonments,
GROUP_CONCAT(DISTINCT
                'ID experiment: --', ve.ID_experiment, '--, ',
                IF(ex.experiment_title IS NULL, '', CONCAT('TITLE experiment: --', ex.experiment_title, '--, ')),
                IF(ve.experiment_duration IS NULL, '', CONCAT('experiment DURATION for victim: --', ve.experiment_duration, '--, ')),
                IF(ve.age_experiment_start IS NULL, '', CONCAT('AGE of victim at start of experiment: --', ve.age_experiment_start, '--, ')),
                IF(ve.exp_start_day IS NULL AND ve.exp_start_month IS NULL AND ve.exp_start_year IS NULL, '', CONCAT('START of experiment: --', IFNULL(ve.exp_start_day, '-'), '.', IFNULL(ve.exp_start_month, '-'), '.', IFNULL(ve.exp_start_year, '-'), '--, ')),
                IF(ve.exp_end_day IS NULL AND ve.exp_end_month IS NULL AND ve.exp_end_year IS NULL, '', CONCAT('END of experiment: --', IFNULL(ve.exp_end_day, '-'), '.', IFNULL(ve.exp_end_month, '-'), '.', IFNULL(ve.exp_end_year, '-'), '--, ')),
                IF(ve.outcome_injuries IS NULL, '', CONCAT('outcome INJURIES: --', ve.outcome_injuries, '--, ')),
                IF(s.english IS NULL, '', CONCAT('SURVIVAL of THIS experiment: --', s.english, '--, ')),
                IF(ve.notes_perpetrator IS NULL, '', CONCAT('notes about PERPETRATOR: --', ve.notes_perpetrator, '--, ')),
                IF(ve.notes IS NULL, '', CONCAT('NOTES concerning experiment: --', ve.notes, '--, ')),
                IF(ve.narratives IS NULL, '', CONCAT('NARRATIVES concerning experiment: --', ve.narratives, '--'))
                 SEPARATOR ' \n') AS 'Victim of Experiment',
GROUP_CONCAT(DISTINCT
                'ID experiment: --', pae.ID_experiment, '--, ',
                IF(ex2.experiment_title IS NULL, '', CONCAT('TITLE experiment: --', ex2.experiment_title, '--, ')),
                IF(pae.experiment_duration IS NULL, '', CONCAT('DURATION of participation in experiment: --', pae.experiment_duration, '--, ')),
                IF(pae.age_experiment_start IS NULL, '', CONCAT('AGE of prisoner assistant when getting involved: --', pae.age_experiment_start, '--, ')),
                IF(pae.exp_start_day IS NULL AND pae.exp_start_month IS NULL AND pae.exp_start_year IS NULL, '', CONCAT('START of experiment: --', IFNULL(pae.exp_start_day, '-'), '.', IFNULL(pae.exp_start_month, '-'), '.', IFNULL(pae.exp_start_year, '-'), '--, ')),
                IF(pae.exp_end_day IS NULL AND pae.exp_end_month IS NULL AND pae.exp_end_year IS NULL, '', CONCAT('END of experiment: --', IFNULL(pae.exp_end_day, '-'), '.', IFNULL(pae.exp_end_month, '-'), '.', IFNULL(pae.exp_end_year, '-'), '--, ')),
                IF(pae.notes_about_involvement IS NULL, '', CONCAT('notes about INVOLVEMENT in this experiment: --', pae.notes_about_involvement, '--, ')),
                IF(pae.narratives IS NULL, '', CONCAT('NARRATIVES concerning experiment: --', pae.narratives, '--, ')),
                CONCAT('Gave TESTIMONY in trial?: --', pae.gave_testimony_in_trial, '--, '),
                IF(pae.ID_role IS NULL, '', CONCAT('ROLE in experiment: --', ro.english, '--, ')),
                IF(pae.role_other IS NULL, '', CONCAT('ROLE in experiment: --', pae.role_other, '--, '))
                 SEPARATOR ' \n') AS 'PRISONER ASSISTANT in experiment',
GROUP_CONCAT(DISTINCT
                'ID hospitalisation: --', h.ID_med_history_hosp, '--, ',
                IF(ih.institution_name IS NULL AND h.institution IS NULL, '', CONCAT('INSTITUTION: --', IFNULL(ih.institution_name, ''), ', ', IFNULL(h.institution, ''), '--, ')),
                IF(ioh.english IS NULL, '', CONCAT('institution order: --', ioh.english, '--, ')),
                IF(h.date_entry_day IS NULL AND h.date_entry_month IS NULL AND h.date_entry_year IS NULL, '', CONCAT('Date ENTRY: --', IFNULL(h.date_entry_day, '-'), '.', IFNULL(h.date_entry_month, '-'), '.', IFNULL(h.date_entry_year, '-'), '--, ')),
                IF(h.age_entry IS NULL, '', CONCAT('AGE at entry: --', h.age_entry, '--, ')),
                IF(h.date_exit_day IS NULL AND h.date_exit_month IS NULL AND h.date_exit_year IS NULL, '', CONCAT('Date EXIT: --', IFNULL(h.date_exit_day, '-'), '.', IFNULL(h.date_exit_month, '-'), '.', IFNULL(h.date_exit_year, '-'), '--, ')),
                IF(h.age_exit IS NULL, '', CONCAT('AGE at exit: --', h.age_exit, '--, ')),
                IF(h.diagnosis IS NULL, '', CONCAT('DIAGNOSIS: --', h.diagnosis, '--, ')),
                IF(ditah.hosp_diagnoses IS NULL, '', CONCAT('DIAGNOSIS TAGS: --', ditah.hosp_diagnoses, '--, ')),
                IF(eh.english IS NULL, '', CONCAT('EDUCATIONAL ABILITIES: --', eh.english, '--, ')),
                IF(bh.english IS NULL, '', CONCAT('BEHAVIOUR:-- ', bh.english, '--, ')),
                IF(dih.english IS NULL, '', CONCAT('DISABILITY: --', dih.english, '--, ')),
                IF(h.autopsy_ref_no IS NULL, '', CONCAT('autopsy REF NO: --', h.autopsy_ref_no, '--, ')),
                IF(h.notes IS NULL, '', CONCAT('hospitalisation NOTES: --', h.notes, '--, ')),
                IF(h.hosp_has_photo = -1, 'medical record has photo', '')
                 SEPARATOR ' \n') AS 'Victim - Hospitalisations (Information often from Krankenakten)',
GROUP_CONCAT(DISTINCT
                'ID brain report: --', b.ID_med_history_brain, '--, ',
                IF(ib.institution_name IS NULL, '', CONCAT('INSTITUTION: --', ib.institution_name, '--, ')),
                IF(b.kwi_researcher IS NULL, '', CONCAT('RESEARCHER: --', b.kwi_researcher, '--, ')),
                IF(b.diagnosis IS NULL, '', CONCAT('diagnosis: --', b.diagnosis, '--, ')),
                IF(ditab.brain_diagnoses IS NULL, '', CONCAT('DIAGNOSIS TAGS: --', ditab.brain_diagnoses, '--, ')),
                IF(b.brain_report_day IS NULL AND b.brain_report_month IS NULL AND b.brain_report_year IS NULL, '', CONCAT('DATE of brain report: --', IFNULL(b.brain_report_day, '-'), '.', IFNULL(b.brain_report_month, '-'), '.', IFNULL(b.brain_report_year, '-'), '--, ')),
                IF(b.ref_no IS NULL, '', CONCAT('brain report REF NO: --', b.ref_no, '--, ')),
                IF(b.notes IS NULL, '', CONCAT('brain report NOTES: --', b.notes, '--, ')),
                IF(b.brain_report_has_photo = -1, 'brain report has photo', '')
                 SEPARATOR ' \n') AS 'Victim - Brain Report',
GROUP_CONCAT(DISTINCT
                'ID tissue: --', t.ID_med_history_tissue, '--, ',
                IF(it.institution_name IS NULL, '', CONCAT('INSTITUTION: --', it.institution_name, '--, ')),
                IF(t.since_day IS NULL AND t.since_month IS NULL AND t.since_year IS NULL, '', CONCAT('in institution SINCE: --', IFNULL(t.since_day, '-'), '.', IFNULL(t.since_month, '-'), '.', IFNULL(t.since_year, '-'), '--, ')),
                IF(ts.english IS NULL, '', CONCAT('tissue STATE: --', ts.english, '--, ')),
                IF(tf.english IS NULL, '', CONCAT('tissue FORM: --', tf.english, '--, ')),
                IF(t.ref_no IS NULL, '', CONCAT('tissue REF NO: --', t.ref_no, '--, ')),
                IF(t.notes IS NULL, '', CONCAT('tissue NOTES: --', t.notes, '--, '))
                 SEPARATOR ' \n') AS 'Victim - Tissue',
v.notes, v.consequential_injuries, v.compensation, v.compensation_details, evs.english AS evaluation_status, v.status_due_to, v.status_notes,
v.residence_after_1945_place, v.residence_after_1945_country, n1945.english AS nationality_after_1945, v.occupation_after_1945, v.notes_after_1945,
GROUP_CONCAT(DISTINCT
                'ID source: --', so.ID_source, '--, ',
                IF(so.source_title IS NULL, '', CONCAT('TITLE source: --', so.source_title, '--, ')),
                IF(vs.location IS NULL, '', CONCAT('LOCATION in source: --', vs.location, '--, ')),
                IF(vs.url IS NULL, '', CONCAT('URL: --', vs.url, '--, ')),
                IF(vs.access_day IS NULL AND vs.access_month IS NULL AND vs.access_year IS NULL, '', CONCAT('ACCESS DATE URL --', IFNULL(vs.access_day, '-'), '.', IFNULL(vs.access_month, '-'), '.', IFNULL(vs.access_year, '-'), '--, ')),
                IF(vs.source_has_photo = -1, 'source has photo', '')
                 SEPARATOR ' \n') AS sources,
GROUP_CONCAT(DISTINCT
                'ID literature: --', l.ID_literature, '--, ',
                IF(l.lit_title IS NULL, '', CONCAT('title literature: --', l.lit_title, '--, ')),
                IF(vl.pages IS NULL, '', CONCAT('pages: --', vl.pages, '--, ')),
                IF(vl.url IS NULL, '', CONCAT('URL: --', vl.url, '--, ')),
                IF(vl.access_day IS NULL AND vl.access_month IS NULL AND vl.access_year IS NULL, '', CONCAT('ACCESS DATE URL --', IFNULL(vl.access_day, '-'), '.', IFNULL(vl.access_month, '-'), '.', IFNULL(vl.access_year, '-'), '--, ')),
                IF(vl.literature_has_photo = -1, 'literature has photo', '')
                 SEPARATOR ' \n') AS literature
FROM nmv__victim v
        LEFT JOIN nmv__victim_name vn ON vn.ID_victim = v.ID_victim
        LEFT JOIN nmv__victim_nametype nt ON nt.ID_nametype = vn.nametype
        LEFT JOIN nmv__country bc ON bc.ID_country = v.ID_birth_country
        LEFT JOIN nmv__nationality n1938 ON n1938.ID_nationality = v.nationality_1938
        LEFT JOIN nmv__country dc ON dc.ID_country = v.ID_death_country
        LEFT JOIN nmv__institution di ON di.ID_institution = v.ID_death_institution
        LEFT JOIN nmv__marital_family_status f ON f.ID_marital_family_status = v.ID_marital_family_status
        LEFT JOIN nmv__religion r ON r.ID_religion = v.religion
        LEFT JOIN nmv__ethnicgroup eg ON eg.ID_ethnicgroup = v.ethnic_group
        LEFT JOIN nmv__education ed ON ed.ID_education = v.ID_education
        LEFT JOIN nmv__occupation o ON o.ID_occupation = v.occupation
        LEFT JOIN nmv__country ac ON ac.ID_country = v.ID_arrest_country
        LEFT JOIN nmv__nationality n1945 ON n1945.ID_nationality = v.nationality_after_1945
        LEFT JOIN nmv__victim_experiment ve ON ve.ID_victim = v.ID_victim
        LEFT JOIN nmv__survival s ON s.ID_survival = ve.ID_survival
        LEFT JOIN nmv__experiment ex ON ex.ID_experiment = ve.ID_experiment
        LEFT JOIN nmv__prisoner_assistant_experiment pae ON pae.ID_victim = v.ID_victim
        LEFT JOIN nmv__experiment ex2 ON ex2.ID_experiment = pae.ID_experiment
        LEFT JOIN nmv__role ro ON ro.ID_role = pae.ID_role
        LEFT JOIN nmv__imprisonment i	ON v.ID_victim = i.ID_victim
        LEFT JOIN nmv__imprisonment_classification ic ON ic.ID_imprisonment = i.ID_imprisonment
        LEFT JOIN nmv__institution ii ON ii.ID_institution = i.ID_institution
        LEFT JOIN (
                SELECT i1.ID_imprisonment, GROUP_CONCAT(vc.english SEPARATOR ', ') AS classifications
                FROM nmv__imprisonment i1
                LEFT JOIN nmv__imprisonment_classification ic ON ic.ID_imprisonment = i1.ID_imprisonment
                LEFT JOIN nmv__victim_classification vc ON vc.ID_classification = ic.ID_classification
                GROUP BY i1.ID_imprisonment) AS vicla ON vicla.ID_imprisonment = i.ID_imprisonment
        LEFT JOIN nmv__victim_evaluation_status evs ON evs.ID_evaluation_status = v.ID_evaluation_status
        LEFT JOIN nmv__victim_source vs ON vs.ID_victim = v.ID_victim
        LEFT JOIN nmv__source so ON so.ID_source = vs.ID_source
        LEFT JOIN nmv__victim_literature vl ON vl.ID_victim = v.ID_victim
        LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
        LEFT JOIN nmv__med_history_hosp h ON h.ID_victim = v.ID_victim
        LEFT JOIN nmv__institution ih ON ih.ID_institution = h.ID_institution
        LEFT JOIN nmv__institution_order ioh ON ioh.ID_institution_order = h.ID_institution_order
        LEFT JOIN nmv__behaviour bh ON bh.ID_behaviour = h.ID_behaviour
        LEFT JOIN nmv__disability dih ON dih.ID_disability = h.ID_disability
        LEFT JOIN nmv__educational_abilities eh ON eh.ID_educational_abilities = h.ID_educational_abilities
        LEFT JOIN (
                SELECT h.ID_med_history_hosp, GROUP_CONCAT(dit.diagnosis SEPARATOR ', ') AS hosp_diagnoses
                FROM nmv__med_history_hosp h
                LEFT JOIN nmv__diagnosis_hosp dth ON dth.ID_med_history_hosp = h.ID_med_history_hosp
                LEFT JOIN nmv__diagnosis_tag dit ON dit.ID_diagnosis = dth.ID_diagnosis
                GROUP BY h.ID_med_history_hosp) AS ditah ON ditah.ID_med_history_hosp = h.ID_med_history_hosp
        LEFT JOIN nmv__med_history_brain b ON b.ID_victim = v.ID_victim
        LEFT JOIN nmv__institution ib ON ib.ID_institution = b.ID_institution
        LEFT JOIN (
                SELECT b.ID_med_history_brain, GROUP_CONCAT(dit.diagnosis SEPARATOR ', ') AS brain_diagnoses
                FROM nmv__med_history_brain b
                LEFT JOIN nmv__diagnosis_brain dtb ON dtb.ID_med_history_brain = b.ID_med_history_brain
                LEFT JOIN nmv__diagnosis_tag dit ON dit.ID_diagnosis = dtb.ID_diagnosis
                GROUP BY b.ID_med_history_brain) AS ditab ON ditab.ID_med_history_brain = b.ID_med_history_brain
        LEFT JOIN nmv__med_history_tissue t ON t.ID_victim = v.ID_victim
        LEFT JOIN nmv__institution it ON it.ID_institution = t.ID_institution
        LEFT JOIN nmv__tissue_state ts ON ts.ID_tissue_state = t.ID_tissue_state
        LEFT JOIN nmv__tissue_form tf ON tf.ID_tissue_form = t.ID_tissue_form";
$prisoner_assistant_query_end = "
GROUP BY v.ID_victim
ORDER BY v.ID_victim ASC";


?>
