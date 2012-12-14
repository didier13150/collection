LANGUAGES := en fr

all: message merge mo info

mo:
	@echo "Compile messages"
	@for lang in $(LANGUAGES) ; do \
		msgfmt -o locale/$$lang/LC_MESSAGES/messages.mo locale/$$lang/LC_MESSAGES/messages.po ; \
	done

message:
	@echo "Extract messages"
	@xgettext -ki18n2html -o collection.pot *.php

merge:
	@echo "Merge messages"
	@for lang in $(LANGUAGES) ; do \
		mv locale/$$lang/LC_MESSAGES/messages.po locale/$$lang/LC_MESSAGES/old.po; \
		echo -ne "\t$$lang "; \
		msgmerge locale/$$lang/LC_MESSAGES/old.po collection.pot -o locale/$$lang/LC_MESSAGES/messages.po ; \
		rm locale/$$lang/LC_MESSAGES/old.po; \
	done

info:
	@echo "Please, translate po file and relaunch this command"
	@echo "make mo"
