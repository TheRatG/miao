import os
import xml.etree.ElementTree as etree

class FileNotFound(Exception):
	pass
class XmlParseError(Exception):
	pass
class PropertyNotFound(Exception):
	pass

#config_file = os.getcwd() + '/tmp/configs/config_build.xml'
config_file = '/www/v2.realty.rbc.ru/data/config.xml'
if not os.path.isfile(config_file):
	raise FileNotFound('Config file "%s" not found' % config_file)

def get(path):
	try:
		tree = etree.parse(config_file)
	except etree.ParseError:
		raise XmlParseError('Config file "%s" is invalid xml' % config_file)
	node = tree.getroot().find(path)
	if node is None:
		raise PropertyNotFound('Property "%s" not found in "%s"' % (path, config_file))
	return node.text
