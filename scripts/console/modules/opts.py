import sys, os, getopt

def parseOptions():
    try:
        opts, args = getopt.getopt(sys.argv[1:], 'c:h', ['config=', 'help'])
    except getopt.GetoptError, err:
        # print help information and exit:
        print str(err) # will print something like "option -a not recognized"
        usage()
        sys.exit(2)
        
    config_file = ''
    for o, a in opts:
        if o in ("-h", "--help"):
            usage()
            sys.exit()
        elif o in ("-c", "--config"):
            config_file = a                
        else:
            assert False, "unhandled option"
            
    if not os.path.isfile(config_file):
        raise FileNotFound('Config file "%s" not found' % config_file)
    
    list = args
    if not len(list):
        print "Error: Empty command"
        usage()
        sys.exit(2)
    
    return list
        
def usage():
    print "\nAvailable commands: add, ren, del"
    print 'Example: miao add Teleprog_FrontOffice_View_Main'
    print ""    
    print "Options:"
    print "-h, --help \tHelp"    
    print "Mandatory arguments to long options are mandatory for short options too."    
    print "-c, --config \tAbsolute path to config.xml file"

def printHeader():
    print 'Miao Console v0.11'
