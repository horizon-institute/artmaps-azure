//
//  
//    ___  _____   ______  __ _   _________ 
//   / _ \/ __/ | / / __ \/ /| | / / __/ _ \
//  / , _/ _/ | |/ / /_/ / /_| |/ / _// , _/
// /_/|_/___/ |___/\____/____/___/___/_/|_| 
//
//  Created by Bart Claessens. bart (at) revolver . be
//

#import "REVClusterAnnotationView.h"

int const CLUSTER_DETAIL_BTN_TAG = 4;

@implementation REVClusterAnnotationView

@synthesize coordinate;

- (id) initWithAnnotation:(id<MKAnnotation>)annotation reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithAnnotation:annotation reuseIdentifier:reuseIdentifier];
    if ( self )
    {
        [self setLabelView];
        [self addRightCalloutAccessoryView];
    }
    return self;
}

-(void) setLabelView{
    label = [[UILabel alloc] initWithFrame:CGRectMake(10,10, 26, 26)];
    [self addSubview:label];
    label.textColor = [UIColor whiteColor];
    label.backgroundColor = [UIColor clearColor];
    label.font = [UIFont boldSystemFontOfSize:11]; 
    label.textAlignment = UITextAlignmentCenter;
    label.shadowColor = [UIColor blackColor];
    label.shadowOffset = CGSizeMake(0,-1);
}

- (void) setClusterText:(NSString *)text
{
    label.text = text;
}

-(void)addRightCalloutAccessoryView{
    self.rightCalloutAccessoryView = [UIButton buttonWithType:UIButtonTypeDetailDisclosure];
    [self.rightCalloutAccessoryView setTag:CLUSTER_DETAIL_BTN_TAG];
}

- (void) dealloc
{
    [label release], label = nil;
    [super dealloc];
}

@end
