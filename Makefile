LOCALE := locale
POT := collection
PO := messages

all: locales mo
locales: extract merge info

mo:
	@echo "Make binary gettext files"
	@for lang in $(shell ls $(LOCALE)) ; do \
		if [ -d "$(LOCALE)/$$lang/LC_MESSAGES" ] ; then \
			msgfmt -o $(LOCALE)/$$lang/LC_MESSAGES/$(PO).mo $(LOCALE)/$$lang/LC_MESSAGES/$(PO).po ; \
		fi ; \
	done

extract:
	@echo "Extract messages from sources"
	@xgettext -ki18n2html -o $(POT).pot *.php

merge: extract
	@echo "Merge messages on po files"
	@for lang in $(shell ls $(LOCALE)) ; do \
		if [ -d "$(LOCALE)/$$lang/LC_MESSAGES" ] ; then \
			mv $(LOCALE)/$$lang/LC_MESSAGES/$(PO).po $(LOCALE)/$$lang/LC_MESSAGES/old.po; \
			echo -ne "\t$$lang "; \
			msgmerge $(LOCALE)/$$lang/LC_MESSAGES/old.po $(POT).pot -o $(LOCALE)/$$lang/LC_MESSAGES/$(PO).po ; \
			rm -f $(LOCALE)/$$lang/LC_MESSAGES/old.po; \
			echo -ne "\t\t";\
			msgfmt --statistics $(LOCALE)/$$lang/LC_MESSAGES/$(PO).po; \
		fi ; \
		rm -f *.mo;\
	done

info:
	@echo "Please, translate po file and launch this command: make"

